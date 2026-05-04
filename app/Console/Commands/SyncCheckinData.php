<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\MocdocCheckinReport;

class SyncCheckinData extends Command
{
    protected $signature   = 'mocdoc:sync-checkin
                              {--days=1 : Past calendar days to sync ending yesterday (1 = yesterday only; ignored if --from/--to or --date)}
                              {--date= : Single day Y-m-d}
                              {--from= : Start Y-m-d (inclusive; must use with --to)}
                              {--to= : End Y-m-d (inclusive; must use with --from)}
                              {--location= : Specific location key, e.g. location1 (default: all)}';

    protected $description = 'Sync MOC Doc check-in into mocdoc_checkin_reports. Default: yesterday only. Range: --from and --to together. One day: --date.';

    /** MOC Doc entity-location key → display name */
    private function locations(): array
    {
        return [
            'location1'  => 'Kerala - Palakkad',
            'location6'  => 'Kerala - Kozhikode',
            'location7'  => 'Erode',
            'location13' => 'Salem (Agraharam)',
            'location14' => 'Tiruppur',
            'location20' => 'Coimbatore - Ganapathy',
            'location21' => 'Hosur',
            'location22' => 'Chennai - Sholinganallur',
            'location23' => 'Chennai - Urapakkam',
            'location24' => 'Chennai - Madipakkam',
            'location25' => 'Salem',
            'location26' => 'Kanchipuram',
            'location27' => 'Coimbatore - Sundarapuram',
            'location28' => 'Trichy',
            'location29' => 'Thiruvallur',
            'location30' => 'Pollachi',
            'location31' => 'Electronic City',
            'location33' => 'Chennai - Tambaram',
            'location34' => 'Tanjore',
            'location35' => 'Konanakunte',
            'location36' => 'Harur',
            'location38' => 'Varadhambalayam',
            'location39' => 'Coimbatore - Thudiyalur',
            'location40' => 'Madurai',
            'location41' => 'Hebbal',
            'location42' => 'Kallakurichi',
            'location43' => 'Vellore',
            'location45' => 'Aathur',
            'location46' => 'Namakal',
            'location47' => 'Dasarahalli',
            'location48' => 'Chengalpattu',
            'location49' => 'Chennai - Vadapalani',
            'location50' => 'Pennagaram',
            'location51' => 'Thirupathur',
            'location52' => 'Sivakasi',
            'location53' => 'Dharmapuri',
            'location54' => 'Nagapattinam',
            'location55' => 'Chennai - Karapakkam',
            'location56' => 'Krishnagiri',
            'location57' => 'Karur',
            'location59' => 'Ariyalur',
            'location60' => 'Mayiladuthurai',
        ];
    }

    public function handle(): int
    {
        $this->info('[SyncCheckinData] Starting…');
        set_time_limit(0);

        $dates = $this->resolveDatesToProcess();
        if ($dates === null) {
            return 1;
        }

        // Resolve locations to process
        $allLocations = $this->locations();
        $specificLoc  = $this->option('location');
        if ($specificLoc) {
            if (!isset($allLocations[$specificLoc])) {
                $this->error("Unknown location key: {$specificLoc}");
                return 1;
            }
            $allLocations = [$specificLoc => $allLocations[$specificLoc]];
        }

        $totalInserted = 0;
        $totalUpdated  = 0;
        $syncedAt      = now();

        // Outer loop: date — process all locations for one date before moving to the next
        foreach ($dates as $date) {
            $dateStr   = $date->format('Ymd');
            $startDate = $dateStr . '00:00:00';
            $endDate   = $dateStr . '23:59:59';

            $this->line("── {$date->toDateString()} ──────────────────────────────");

            foreach ($allLocations as $locKey => $locName) {
                $this->line("  → {$locName} ({$locKey})");

                $apiData = $this->callApi($startDate, $endDate, $locKey);

                if (empty($apiData['checkinlist'])) {
                    $this->line('     No records.');
                    usleep(300000);
                    continue;
                }

                foreach ($apiData['checkinlist'] as $item) {
                    $checkinDate = !empty($item['date'])
                        ? Carbon::createFromFormat('Ymd', $item['date'])->toDateString()
                        : $date->toDateString();

                    $dob = null;
                    if (!empty($item['patient']['dob'])) {
                        try {
                            $dob = Carbon::createFromFormat('Ymd', $item['patient']['dob'])->toDateString();
                        } catch (\Exception $e) {}
                    }

                    $checkinKey = $item['checkinkey'] ?? null;

                    $payload = [
                        'checkinkey'           => $checkinKey,
                        'phid'                 => $item['patient']['phid'] ?? null,
                        'checkin_date'         => $checkinDate,
                        'checkin_time'         => $item['start'] ?? null,
                        'patient_name'         => trim(($item['patient']['title'] ?? '').' '.($item['patient']['name'] ?? '').($item['patient']['lname'] ? ' '.$item['patient']['lname'] : '')),
                        'mobile'               => $item['patient']['mobile'] ?? null,
                        'dob'                  => $dob,
                        'age'                  => $item['patient']['age'] ?? null,
                        'gender'               => $item['patient']['gender'] ?? null,
                        'purpose'              => $item['purpose'] ?? null,
                        'ptsource'             => $item['patient']['ptsource'] ?? null,
                        'city'                 => $item['patient']['address']['city'] ?? null,
                        'state'                => $item['patient']['address']['state'] ?? null,
                        'bookeddr_name'        => $item['bookeddr_name'] ?? null,
                        'visittype'            => $item['visittype'] ?? null,
                        'opno'                 => $item['opno'] ?? null,
                        'mocdoc_location_key'  => $locKey,
                        'mocdoc_location_name' => $locName,
                        'synced_at'            => $syncedAt,
                    ];

                    // Use checkinkey as unique identifier when available; fall back to date+time+mobile+location
                    if ($checkinKey) {
                        $existing = MocdocCheckinReport::where('checkinkey', $checkinKey)->first();
                    } else {
                        $existing = MocdocCheckinReport::where('checkin_date', $checkinDate)
                            ->where('checkin_time', $payload['checkin_time'])
                            ->where('mobile', $payload['mobile'])
                            ->where('mocdoc_location_key', $locKey)
                            ->first();
                    }

                    if ($existing) {
                        $existing->update($payload);
                        $totalUpdated++;
                    } else {
                        MocdocCheckinReport::create($payload);
                        $totalInserted++;
                    }
                }

                usleep(300000); // 300ms between location API calls
            }
        }

        $this->info("[SyncCheckinData] Done — Inserted: {$totalInserted}, Updated: {$totalUpdated}");
        return 0;
    }

    private function callApi(string $startDate, string $endDate, string $locationKey): array
    {
        $url        = 'https://mocdoc.in/api/checkedin/draravinds-ivf';
        $postFields = "startdate={$startDate}&enddate={$endDate}&entitylocation={$locationKey}";
        $headers    = [
            'md-authorization: MD 7b40af0edaf0ad75:jR1+YyQZVWCIIaXlgxt1z8uixQ4=',
            'Date: Mon, 31 Mar 2025 08:05:38 GMT',
            'Content-Type: application/x-www-form-urlencoded',
        ];

        $maxRetries = 5;
        $delay      = 2;
        for ($i = 0; $i < $maxRetries; $i++) {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL            => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 60,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_CUSTOMREQUEST  => 'POST',
                CURLOPT_POSTFIELDS     => $postFields,
                CURLOPT_HTTPHEADER     => $headers,
                CURLOPT_SSL_VERIFYPEER => false,
            ]);
            $response = curl_exec($curl);
            $status   = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            if ($status === 200) return json_decode($response, true) ?? [];
            if ($status === 429) { sleep($delay); $delay *= 2; } else break;
        }
        return [];
    }

    /**
     * Build list of calendar days to sync (Asia/Kolkata).
     * Priority: --from + --to (both required)  >  --date  >  --days (default 1 = yesterday only).
     *
     * @return list<Carbon>|null null on validation error
     */
    private function resolveDatesToProcess(): ?array
    {
        $tz = 'Asia/Kolkata';

        $fromOpt = Carbon::createFromDate(2025, 4, 01)->startOfDay();
        $toOpt   = Carbon::yesterday()->endOfDay();
        $fromSet = $fromOpt !== null && $fromOpt !== '';
        $toSet   = $toOpt   !== null && $toOpt   !== '';

        if ($fromSet || $toSet) {
            if ($fromSet xor $toSet) {
                $this->error('For a date range pass both --from=Y-m-d and --to=Y-m-d (inclusive). For one day use --date=Y-m-d. Default with no flags: yesterday (--days=1).');
                return null;
            }
            try {
                $start = Carbon::parse($fromOpt, $tz)->startOfDay();
                $end   = Carbon::parse($toOpt,   $tz)->startOfDay();
            } catch (\Exception $e) {
                $this->error('Invalid --from or --to (use Y-m-d).');
                return null;
            }
            if ($end->lt($start)) {
                $this->error('--to must be on or after --from.');
                return null;
            }
            $dates = [];
            for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
                $dates[] = $d->copy();
            }
            $this->line("Date range: {$start->toDateString()} → {$end->toDateString()} ({$tz}, ".count($dates)." day(s)).");
            return $dates;
        }

        if ($specificDate = $this->option('date')) {
            if ($specificDate === '') {
                $this->error('Pass a value for --date=Y-m-d, or use both --from and --to for a range.');
                return null;
            }
            try {
                $d = Carbon::parse($specificDate, $tz)->startOfDay();
                $this->line("Date: {$d->toDateString()} ({$tz}, single day).");
                return [$d];
            } catch (\Exception $e) {
                $this->error("Invalid --date: {$specificDate}");
                return null;
            }
        }

        $days  = max(1, (int) $this->option('days'));
        $dates = [];
        $today = Carbon::today($tz);
        for ($i = 1; $i <= $days; $i++) {
            $dates[] = $today->copy()->subDays($i);
        }
        if ($days === 1) {
            $this->line('Date: '.$dates[0]->toDateString()." ({$tz}, yesterday only — use --from/--to for a range).");
        } else {
            $this->line("Dates: last {$days} days ending ".$dates[0]->toDateString()." ({$tz}).");
        }
        return $dates;
    }
}
