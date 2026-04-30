<?php

namespace App\Services;

use App\Models\BranchFinancialReport;
use App\Models\RadiantCashPickup;
use App\Models\TblLocationModel;
use App\Models\TblPoEmail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

class RadiantMismatchService
{
    private const MATCH_PCT = 0.01;  // ≤1%  → Match
    private const CLOSE_PCT = 0.10;  // ≤10% → Close

    private const LOG_CHANNEL = 'radiant_mismatch';

    /** @param array<string, mixed> $context */
    private function log(string $level, string $message, array $context = []): void
    {
        Log::channel(self::LOG_CHANNEL)->log($level, '[RadiantMismatch] ' . $message, $context);
    }

    /* ══════════════════════════════════════════════════════
       PUBLIC: run comparison for one date, send email if needed
       Returns result array usable in both AJAX response & upload.
    ══════════════════════════════════════════════════════ */
    public function checkAndAlert(string $date, string $triggeredBy = 'System'): array
    {
        $parsedDate = Carbon::parse($date)->toDateString();

        $this->log('info', 'check_and_alert.start', [
            'date'         => $parsedDate,
            'triggered_by' => $triggeredBy,
        ]);

        /* 1 — Fetch all pickups for the date */
        $pickups = RadiantCashPickup::whereDate('pickup_date_parsed', $parsedDate)
            ->orderBy('location')
            ->get();

        if ($pickups->isEmpty()) {
            $this->log('info', 'check_and_alert.no_pickups', ['date' => $parsedDate]);

            return [
                'date'         => $parsedDate,
                'found'        => false,
                'total'        => 0,
                'matched'      => 0,
                'mismatch'     => 0,
                'email_sent'   => false,
                'recipients'   => 0,
                'message'      => "No pickup records for {$parsedDate}.",
                'all_matched'  => false,
            ];
        }

        /* 2 — Run per-row comparison */
        $mismatches   = [];
        $matchedCount = 0;

        foreach ($pickups as $pickup) {
            $row = $this->compareOne($pickup, $parsedDate);
            if ($row['has_mismatch']) {
                $mismatches[] = $row;
            } else {
                $matchedCount++;
            }
        }
        $this->log('info', 'mismatch data', ['mismatches' => $mismatches]);

        $total         = $pickups->count();
        $mismatchCount = count($mismatches);

        $this->log('info', 'check_and_alert.comparison_done', [
            'date'            => $parsedDate,
            'triggered_by'    => $triggeredBy,
            'total_pickups'   => $total,
            'matched'         => $matchedCount,
            'mismatch_count'  => $mismatchCount,
            'mismatch_sample' => array_slice(array_map(function ($m) {
                return [
                    'pickup_id'   => $m['pickup_id'],
                    'location'    => $m['location'],
                    'bfr_status'  => $m['bfr_status'],
                    'bank_status' => $m['bank_status'],
                    'rcp_amount'  => $m['rcp_amount'],
                ];
            }, $mismatches), 0, 25),
        ]);

        /* 3 — All matched — no email */
        if (empty($mismatches)) {
            $this->log('info', 'check_and_alert.all_matched_no_email', [
                'date'  => $parsedDate,
                'total' => $total,
            ]);

            return [
                'date'        => $parsedDate,
                'found'       => true,
                'total'       => $total,
                'matched'     => $matchedCount,
                'mismatch'    => 0,
                'email_sent'  => false,
                'recipients'  => 0,
                'all_matched' => true,
                'message'     => "✓ {$parsedDate}: All {$total} records matched — no alert email sent.",
            ];
        }

        /* 4 — Resolve recipients (email + SMS from same Radiant / Email Master rows) */
        [$toEmails, $ccEmails] = $this->resolveRecipients();
        $smsRecipients       = $this->resolveSmsRecipients();

        $this->log('info', 'check_and_alert.recipients_resolved', [
            'date'         => $parsedDate,
            'to_count'     => count($toEmails),
            'cc_count'     => count($ccEmails),
            'sms_targets'  => count($smsRecipients),
            'to_domains'   => $this->maskEmailsForLog($toEmails),
        ]);

        if (empty($toEmails) && empty($smsRecipients)) {
            $this->log('warning', 'check_and_alert.no_recipients', [
                'date'           => $parsedDate,
                'mismatch_count' => $mismatchCount,
            ]);

            return [
                'date'        => $parsedDate,
                'found'       => true,
                'total'       => $total,
                'matched'     => $matchedCount,
                'mismatch'    => $mismatchCount,
                'email_sent'  => false,
                'sms_sent'    => 0,
                'recipients'  => 0,
                'all_matched' => false,
                'message'     => "⚠ {$parsedDate}: {$mismatchCount} mismatch(es) found but no email or SMS recipients configured (Radiant / Email Master).",
            ];
        }

        /* 5 — Send email (optional) */
        $emailSent  = false;
        $emailError = null;
        if (! empty($toEmails)) {
            try {
                Mail::send(
                    'emails.radiant_mismatch_alert',
                    [
                        'mismatches'    => $mismatches,
                        'date'          => $parsedDate,
                        'totalCount'    => $total,
                        'matchedCount'  => $matchedCount,
                        'mismatchCount' => $mismatchCount,
                        'sentBy'        => $triggeredBy,
                    ],
                    function ($m) use ($toEmails, $ccEmails, $parsedDate, $mismatchCount) {
                        $label = $mismatchCount > 1 ? 'mismatches' : 'mismatch';
                        $m->to($toEmails)
                          ->subject("⚠ Radiant Cash Mismatch Alert – {$parsedDate} ({$mismatchCount} {$label})");
                        if (! empty($ccEmails)) {
                            $m->cc($ccEmails);
                        }
                    }
                );
                $emailSent = true;

                $this->log('info', 'check_and_alert.email_sent', [
                    'date'           => $parsedDate,
                    'mismatch_count' => $mismatchCount,
                    'to_count'       => count($toEmails),
                    'cc_count'       => count($ccEmails),
                ]);
            } catch (\Exception $e) {
                $emailError = $e->getMessage();
                $this->log('error', 'check_and_alert.email_failed', [
                    'date'           => $parsedDate,
                    'mismatch_count' => $mismatchCount,
                    'exception'      => $emailError,
                    'file'           => $e->getFile(),
                    'line'           => $e->getLine(),
                ]);
            }
        }

        /* 6 — SMS: same DLT template as deadline (Dear {name + detail} You have received…) */
        $smsSent = 0;
        if (! empty($smsRecipients)) {
            $smsSvc = app(HmsDltPortalSmsService::class);
            $label  = $mismatchCount > 1 ? 'mismatches' : 'mismatch';
            $detail = "Radiant {$parsedDate} {$mismatchCount} {$label} amount";
            foreach ($smsRecipients as $rec) {
                $res = $smsSvc->send($rec['mobile'], $rec['name'], $detail, 'radiant_mismatch');
                if ($res['success']) {
                    $smsSent++;
                }
            }
            $this->log('info', 'check_and_alert.sms_batch_done', [
                'date'      => $parsedDate,
                'sms_sent'  => $smsSent,
                'attempted' => count($smsRecipients),
            ]);
        }

        $parts = [];
        if ($emailSent) {
            $parts[] = 'email to ' . count($toEmails) . ' address(es)';
        } elseif (! empty($toEmails) && $emailError) {
            $parts[] = 'email failed: ' . $emailError;
        }
        if ($smsSent > 0) {
            $parts[] = "SMS {$smsSent}/" . count($smsRecipients);
        } elseif (! empty($smsRecipients)) {
            $parts[] = 'SMS not accepted for all numbers';
        }
        $summary = $parts !== [] ? implode('; ', $parts) : 'no channel succeeded';

        $out = [
            'date'         => $parsedDate,
            'found'        => true,
            'total'        => $total,
            'matched'      => $matchedCount,
            'mismatch'     => $mismatchCount,
            'email_sent'   => $emailSent,
            'sms_sent'     => $smsSent,
            'recipients'   => count($toEmails),
            'all_matched'  => false,
            'email_error'  => $emailError,
            'message'      => "✓ {$parsedDate}: {$summary} — {$mismatchCount} mismatch(es) in {$total} records.",
        ];

        $this->log('info', 'check_and_alert.complete', [
            'date'       => $parsedDate,
            'email_sent' => $emailSent,
            'sms_sent'   => $smsSent,
            'mismatch'   => $mismatchCount,
            'matched'    => $matchedCount,
            'total'      => $total,
        ]);

        return $out;
    }

    /** Redact local-part of emails for logs (keeps domain for debugging). */
    private function maskEmailsForLog(array $emails): array
    {
        return array_values(array_map(function ($email) {
            $email = strtolower(trim((string) $email));
            if ($email === '' || !str_contains($email, '@')) {
                return '(invalid)';
            }
            [$local, $domain] = explode('@', $email, 2);

            return (strlen($local) > 0 ? '*' . substr($local, -1) : '*') . '@' . $domain;
        }, $emails));
    }

    /**
     * Restrict a bank_statements query to rows that count toward Radiant pickup
     * reconciliation for the given location. Three strategies are applied (OR):
     *   1. Description contains "BY CASH / BYCASH + location"
     *   2. radiant_match_against keyword matches the location name
     *   3. radiant_cash_pickup_id is directly linked to the given $pickupId
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  int|null  $pickupId  If provided, rows with radiant_cash_pickup_id = this are also included.
     */
    public static function applyRadiantBankLocationMatch($query, string $locationName, ?int $pickupId = null): void
    {
        $locationName = trim($locationName);
        $hasPickupId  = $pickupId && Schema::hasColumn('bank_statements', 'radiant_cash_pickup_id');

        // No criteria at all — return nothing
        if ($locationName === '' && ! $hasPickupId) {
            $query->whereRaw('1 = 0');
            return;
        }

        $query->where(function ($q) use ($locationName, $hasPickupId, $pickupId) {

            // Strategy 1 & 2 — description / keyword (only when location name is known)
            if ($locationName !== '') {
                // 1. Description pattern: BY CASH or BYCASH + location
                $q->orWhere(function ($q1) use ($locationName) {
                    $q1->where('description', 'like', '%BY CASH%'.$locationName.'%')
                        ->orWhere('description', 'like', '%BYCASH%'.$locationName.'%');
                });

                // 2. radiant_match_against keyword matches location name (fuzzy)
                if (Schema::hasColumn('bank_statements', 'radiant_match_against')) {
                    $q->orWhere(function ($q2) use ($locationName) {
                        $q2->whereNotNull('radiant_match_against')
                            ->where('radiant_match_against', '!=', '')
                            ->where(function ($q3) use ($locationName) {
                                $q3->whereRaw('LOWER(TRIM(radiant_match_against)) = LOWER(?)', [$locationName])
                                    ->orWhereRaw('LOWER(?) LIKE CONCAT("%", LOWER(TRIM(radiant_match_against)), "%")', [$locationName])
                                    ->orWhereRaw('LOWER(TRIM(radiant_match_against)) LIKE CONCAT("%", LOWER(?), "%")', [$locationName]);
                            });
                    });
                }
            }

            // Strategy 3 — direct radiant_cash_pickup_id link
            if ($hasPickupId) {
                $q->orWhere('radiant_cash_pickup_id', $pickupId);
            }
        });
    }

    /* ══════════════════════════════════════════════════════
       Compare one pickup row vs BFR + Bank
    ══════════════════════════════════════════════════════ */
    public function compareOne(RadiantCashPickup $pickup, string $date): array
    {
        $locationName = trim($pickup->location ?? '');
        $rcpAmount    = (float) ($pickup->pickup_amount ?? 0);

        /* Match TblLocation */
        $tblLocation = $this->findLocation($locationName);

        /* Branch Financial Report */
        $bfrAmount  = 0;
        $bfrRecords = 0;
        $bfrStatus  = 'no_data';

        if ($tblLocation) {
            $reports = BranchFinancialReport::where('branch_id', $tblLocation->id)
                ->whereDate('report_date', $date)
                ->get();

            if ($reports->isNotEmpty()) {
                $bfrAmount  = (float) $reports->sum('radiant_collection_amount');
                $bfrRecords = $reports->count();
                $bfrStatus  = $this->matchStatus($rcpAmount, $bfrAmount);
            }
        }

        /* Bank Statement */
        $bankAmount  = 0;
        $bankEntries = 0;
        $bankStatus  = 'no_data';

        if ($locationName || $pickup->id) {
            $pd     = Carbon::parse($date);
            $bkFrom = $pd->copy()->subDay()->toDateString();
            $bkTo   = $pd->copy()->addDay()->toDateString();
            $pickupId = (int) $pickup->id;

            // Build query: (date-window AND desc/keyword match) OR (direct pickup_id link)
            $bankQuery = DB::table('bank_statements')
                ->where(function ($outer) use ($bkFrom, $bkTo, $locationName, $pickupId) {
                    // Strategy 1 & 2: description / keyword, confined to ±1 day window
                    if ($locationName !== '') {
                        $outer->orWhere(function ($dated) use ($bkFrom, $bkTo, $locationName) {
                            $dated->whereRaw("STR_TO_DATE(transaction_date, '%d/%b/%Y') BETWEEN ? AND ?", [$bkFrom, $bkTo]);
                            self::applyRadiantBankLocationMatch($dated, $locationName, null);
                        });
                    }
                    // Strategy 3: direct radiant_cash_pickup_id — no date restriction
                    if ($pickupId && Schema::hasColumn('bank_statements', 'radiant_cash_pickup_id')) {
                        $outer->orWhere('radiant_cash_pickup_id', $pickupId);
                    }
                });

            $rows = $bankQuery->get();

            if ($rows->isNotEmpty()) {
                $bankAmount  = (float) $rows->sum('deposit');
                $bankEntries = $rows->count();
                $bankStatus  = $this->matchStatus($rcpAmount, $bankAmount);
            }
        }

        $hasMismatch = in_array('mismatch', [$bfrStatus, $bankStatus])
                    || in_array('no_data',  [$bfrStatus, $bankStatus]);

        return [
            'has_mismatch'    => $hasMismatch,
            'pickup_id'       => $pickup->id,
            'date'            => $pickup->pickup_date,
            'location'        => $locationName,
            'region'          => $pickup->region,
            'state'           => $pickup->state_name,
            'hci_slip'        => $pickup->hci_slip_no,
            'deposit_mode'    => $pickup->deposit_mode,
            'rcp_amount'      => $rcpAmount,
            'bfr_amount'      => $bfrAmount,
            'bfr_records'     => $bfrRecords,
            'bfr_status'      => $bfrStatus,
            'bfr_location'    => $tblLocation ? $tblLocation->name : null,
            'bfr_zone'        => $tblLocation ? optional($tblLocation->zone)->name : null,
            'bank_amount'     => $bankAmount,
            'bank_entries'    => $bankEntries,
            'bank_status'     => $bankStatus,
            'difference_bfr'  => $rcpAmount - $bfrAmount,
            'difference_bank' => $rcpAmount - $bankAmount,
        ];
    }

    /* ── Fuzzy location match ── */
    public function findLocation(string $name): ?TblLocationModel
    {
        if (!$name) return null;

        $loc = TblLocationModel::whereRaw('LOWER(TRIM(name)) = LOWER(?)', [$name])->first();
        if (!$loc) {
            $loc = TblLocationModel::whereRaw(
                'LOWER(TRIM(?)) LIKE CONCAT(\'%\', LOWER(TRIM(name)), \'%\')', [$name]
            )->first();
        }
        if (!$loc) {
            $loc = TblLocationModel::where('name', 'like', '%' . $name . '%')->first();
        }
        if (!$loc) {
            $first = explode(' ', $name)[0];
            if (strlen($first) > 3) {
                $loc = TblLocationModel::where('name', 'like', '%' . $first . '%')->first();
            }
        }

        return $loc;
    }

    /**
     * Walk a filtered pickup query and count (and optionally sample) BFR + bank reconciliation rows.
     *
     * @param  Builder<\App\Models\RadiantCashPickup>  $query
     * @return array{
     *     match_count: int,
     *     mismatch_count: int,
     *     matched: list<array<string, mixed>>,
     *     mismatched: list<array<string, mixed>>
     * }
     */
    public function reconcileDashboardState(Builder $query, bool $includeLists = false, int $listCap = 400): array
    {
        $matchCount = 0;
        $mismatchCount = 0;
        $matched = [];
        $mismatched = [];

        foreach ((clone $query)->orderBy('id')->cursor() as $pickup) {
            $row = $this->pickupReconcileRow($pickup);
            if ($row['has_mismatch']) {
                $mismatchCount++;
                if ($includeLists && count($mismatched) < $listCap) {
                    $mismatched[] = $row['summary'];
                }
            } else {
                $matchCount++;
                if ($includeLists && count($matched) < $listCap) {
                    $matched[] = $row['summary'];
                }
            }
        }

        return [
            'match_count' => $matchCount,
            'mismatch_count' => $mismatchCount,
            'matched' => $matched,
            'mismatched' => $mismatched,
        ];
    }

    /**
     * @return array{has_mismatch: bool, summary: array<string, mixed>}
     */
    protected function pickupReconcileRow(RadiantCashPickup $pickup): array
    {
        $parsed = $pickup->pickup_date_parsed;
        if ($parsed === null) {
            return [
                'has_mismatch' => true,
                'summary' => [
                    'pickup_id' => $pickup->id,
                    'pickup_date' => $pickup->pickup_date,
                    'pickup_date_parsed' => null,
                    'location' => trim((string) ($pickup->location ?? '')),
                    'state' => $pickup->state_name,
                    'region' => $pickup->region,
                    'rcp_amount' => (float) ($pickup->pickup_amount ?? 0),
                    'bfr_status' => 'no_data',
                    'bfr_amount' => 0.0,
                    'bank_status' => 'no_data',
                    'bank_amount' => 0.0,
                    'hci_slip' => $pickup->hci_slip_no,
                    'reason' => 'Missing parsed pickup date',
                ],
            ];
        }

        $dateStr = $parsed instanceof \DateTimeInterface
            ? Carbon::instance($parsed)->toDateString()
            : Carbon::parse($parsed)->toDateString();

        $cmp = $this->compareOne($pickup, $dateStr);
        $summary = [
            'pickup_id' => $pickup->id,
            'pickup_date' => $pickup->pickup_date,
            'pickup_date_parsed' => $dateStr,
            'location' => $cmp['location'],
            'state' => $cmp['state'],
            'region' => $cmp['region'],
            'rcp_amount' => $cmp['rcp_amount'],
            'bfr_status' => $cmp['bfr_status'],
            'bfr_amount' => $cmp['bfr_amount'],
            'bank_status' => $cmp['bank_status'],
            'bank_amount' => $cmp['bank_amount'],
            'hci_slip' => $cmp['hci_slip'],
        ];

        return [
            'has_mismatch' => $cmp['has_mismatch'],
            'summary' => $summary,
        ];
    }

    /* ── Amount status ── */
    public function matchStatus(float $rcp, float $other): string
    {
        if ($rcp == 0 && $other == 0) return 'match';
        if ($other == 0)              return 'no_data';

        $pct = $rcp > 0 ? abs($rcp - $other) / $rcp : 1;

        if ($pct <= self::MATCH_PCT) return 'match';
        if ($pct <= self::CLOSE_PCT) return 'close';

        return 'mismatch';
    }

    /** Active Email Master rows for Radiant (fallback: all active). */
    private function radiantNotificationConfigs(): Collection
    {
        $configs = TblPoEmail::where('status', 1)
            ->where(function ($q) {
                $q->where('menu_type', 'like', '%Radiant%')
                  ->orWhere('menu_type', 'like', '%radiant%');
            })
            ->get();

        if ($configs->isEmpty()) {
            $configs = TblPoEmail::where('status', 1)->get();
        }

        return $configs;
    }

    /* ── Email recipients from Email Master ── */
    private function resolveRecipients(): array
    {
        $configs = $this->radiantNotificationConfigs();

        $toEmails = $configs
            ->map(fn ($r) => $r->to_email ?: $r->email)
            ->filter()->unique()->values()->toArray();

        $ccEmails = $configs
            ->flatMap(function ($r) {
                $cc = is_string($r->cc_emails) ? json_decode($r->cc_emails, true) : ($r->cc_emails ?? []);

                return is_array($cc) ? $cc : [];
            })
            ->filter()->unique()->values()->toArray();

        return [$toEmails, $ccEmails];
    }

    /**
     * @return list<array{mobile: string, name: string}>
     */
    private function resolveSmsRecipients(): array
    {
        $seen = [];
        $out  = [];
        foreach ($this->radiantNotificationConfigs() as $r) {
            $raw = preg_replace('/\s+/u', '', (string) ($r->mobile_number ?? '')) ?? '';
            if ($raw === '') {
                continue;
            }
            if (isset($seen[$raw])) {
                continue;
            }
            $seen[$raw] = true;
            $name = trim((string) ($r->created_by ?? '')) ?: 'Team';
            $out[] = ['mobile' => $raw, 'name' => $name];
        }

        return $out;
    }
}
