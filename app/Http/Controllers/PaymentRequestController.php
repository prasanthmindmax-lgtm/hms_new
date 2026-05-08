<?php

namespace App\Http\Controllers;

use App\Models\PaymentRequest;
use App\Models\TblBillPayLines;
use App\Models\Tblbill;
use App\Models\Tblbillpay;
use App\Models\TblLocationModel;
use App\Models\TblPurchaseorder;
use App\Models\TblVendorHistory;
use App\Models\Tblcompany;
use App\Models\Tblvendor;
use App\Models\TblZonesModel;
use App\Support\CreateFormDuration;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Support\Carbon;

class PaymentRequestController extends Controller
{
    private function userRow(): object
    {
        $u = auth()->user();
        if (! $u) {
            abort(403);
        }

        return is_object($u) ? $u : (object) (array) $u;
    }

    /** Super Admin (access_limits 1): no filter. Everyone else: only rows they created. */
    private function isPaymentRequestSuperAdmin(object $user): bool
    {
        return (int) ($user->access_limits ?? 0) === 1;
    }

    private function scopePaymentRequestsForUser(Builder $query, object $user): void
    {
        if ($this->isPaymentRequestSuperAdmin($user)) {
            return;
        }
        $uid = (int) auth()->id();
        if ($uid > 0) {
            $query->where('created_by', $uid);
        }
    }

    private function nextRequestNumber(): string
    {
        $y = date('Y');
        $p = 'PAY-'.$y.'-';
        $n = (int) PaymentRequest::query()->where('request_no', 'like', $p.'%')->count() + 1;

        return $p.str_pad((string) $n, 5, '0', STR_PAD_LEFT);
    }

    private function locationDropdownData(): array
    {
        return [
            'companies' => Tblcompany::query()->orderBy('company_name')->get(['id', 'company_name']),
            'zones' => TblZonesModel::query()->orderBy('name')->get(['id', 'name']),
            'branches' => TblLocationModel::query()->orderBy('name')->get(['id', 'name', 'zone_id']),
        ];
    }

    private function saveUploaded(UploadedFile $file, string $subDir): string
    {
        $uploadPath = public_path('payment_request_attachments');
        if (! File::isDirectory($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }
        $name = time().'_'.preg_replace('/\s+/', '_', $file->getClientOriginalName());
        $name = basename(str_replace(["\0", '/', '\\'], '', $name));
        $file->move($uploadPath, $name);

        return 'payment_request_attachments/'.$name;
    }

    private function resolveVendorTableIdForBill(Tblbill $bill): ?int
    {
        $raw = $bill->vendor_id;
        if ($raw === null || $raw === '') {
            return null;
        }
        $byId = Tblvendor::query()->where('id', $raw)->first();
        if ($byId) {
            return (int) $byId->id;
        }
        $byCode = Tblvendor::query()->where('vendor_id', $raw)->first();

        return $byCode ? (int) $byCode->id : null;
    }

    private function paymentRequestTotalsForPurchaseOrder(int $poId): array
    {
        $sumPendingAndApproved = (float) PaymentRequest::query()
            ->where('purchase_order_id', $poId)
            ->countingTowardPo()
            ->sum('amount');
        $sumApproved = (float) PaymentRequest::query()
            ->where('purchase_order_id', $poId)
            ->where('status', PaymentRequest::STATUS_APPROVED)
            ->sum('amount');
        $last = PaymentRequest::query()
            ->where('purchase_order_id', $poId)
            ->where('status', PaymentRequest::STATUS_APPROVED)
            ->orderByDesc('id')
            ->value('amount');
        $lastAmt = $last !== null ? (float) $last : 0.0;

        return [
            'sum_pending_and_approved' => $sumPendingAndApproved,
            'sum_approved_only' => $sumApproved,
            'last_approved_amount' => $lastAmt,
        ];
    }

    private function formatBillPayLineDateForDisplay(?string $paymentDate): string
    {
        if ($paymentDate === null || trim($paymentDate) === '') {
            return '';
        }
        $paymentDate = trim($paymentDate);
        try {
            if (str_contains($paymentDate, '/')) {
                return Carbon::createFromFormat('d/m/Y', $paymentDate)->format('d-M-Y');
            }

            return Carbon::parse($paymentDate)->format('d-M-Y');
        } catch (\Throwable $e) {
            return $paymentDate;
        }
    }

    /**
     * Bill Made / NEFT style payment lines for a single vendor bill.
     *
     * @return array<int, array{date: string, amount: float, caption: string}>
     */
    private function billPayLinesHistoryForBill(int $billId): array
    {
        $lines = TblBillPayLines::query()
            ->where('bill_id', $billId)
            ->orderBy('id')
            ->get(['id', 'payment_date', 'amount']);

        $out = [];
        foreach ($lines as $line) {
            $amt = (float) ($line->amount ?? 0);
            if ($amt < 0.0001) {
                continue;
            }
            $dateStr = $this->formatBillPayLineDateForDisplay($line->payment_date ?? null);
            $out[] = [
                'date' => $dateStr,
                'amount' => $amt,
                'caption' => 'Bill / NEFT payment',
            ];
        }

        return $out;
    }

    /**
     * Bill Made / NEFT style payments on any vendor bill linked to this PO (same source as bill “Paid on …” lines).
     *
     * @return array<int, array{date: string, amount: float, caption: string, _ts: float}>
     */
    private function billPayLinesHistoryForPurchaseOrder(int $poId): array
    {
        $billIds = Tblbill::query()
            ->where(function ($q) {
                $q->where('delete_status', 0)->orWhereNull('delete_status');
            })
            ->where('purchase_id', $poId)
            ->pluck('id');

        if ($billIds->isEmpty()) {
            return [];
        }

        $lines = TblBillPayLines::query()
            ->whereIn('bill_id', $billIds)
            ->with(['Bill:id,bill_gen_number,bill_number'])
            ->orderBy('id')
            ->get(['id', 'bill_id', 'payment_date', 'amount', 'bill_number']);

        $out = [];
        foreach ($lines as $line) {
            $amt = (float) ($line->amount ?? 0);
            if ($amt < 0.0001) {
                continue;
            }
            $dateStr = $this->formatBillPayLineDateForDisplay($line->payment_date ?? null);
            $billRef = '';
            if ($line->relationLoaded('Bill') && $line->Bill) {
                $billRef = trim((string) ($line->Bill->bill_gen_number ?? ''));
                if ($billRef === '') {
                    $billRef = trim((string) ($line->Bill->bill_number ?? ''));
                }
            }
            if ($billRef === '') {
                $billRef = trim((string) ($line->bill_number ?? ''));
            }
            $caption = 'Paid on '.$dateStr.' · Bill / NEFT (Bill Made)';
            if ($billRef !== '') {
                $caption .= ' · '.$billRef;
            }
            $ts = 0.0;
            if ($line->payment_date !== null && trim((string) $line->payment_date) !== '') {
                try {
                    $raw = trim((string) $line->payment_date);
                    if (str_contains($raw, '/')) {
                        $ts = (float) Carbon::createFromFormat('d/m/Y', $raw)->timestamp;
                    } else {
                        $ts = (float) Carbon::parse($raw)->timestamp;
                    }
                } catch (\Throwable $e) {
                    $ts = 0.0;
                }
            }
            $ts += ((int) $line->id) / 1_000_000;

            $out[] = [
                'date' => $dateStr,
                'amount' => $amt,
                'caption' => $caption,
                '_ts' => $ts,
            ];
        }

        return $out;
    }
    
    private function paymentRequestHistoryForPurchaseOrderNonBillBacked(int $poId): array
    {
        return PaymentRequest::query()
            ->where('purchase_order_id', $poId)
            ->where(function (Builder $outer) {
                $outer->where('status', PaymentRequest::STATUS_PENDING)
                    ->orWhere(function (Builder $q) {
                        $q->where('status', PaymentRequest::STATUS_APPROVED)
                            ->where(function (Builder $b) {
                                $b->whereNull('bill_id')->orWhere('bill_id', 0);
                            })
                            ->whereDoesntHave('linkedBills');
                    });
            })
            ->orderByRaw('COALESCE(reviewed_at, created_at) ASC')
            ->orderBy('id')
            ->get(['id', 'amount', 'status', 'created_at', 'reviewed_at', 'request_no', 'payment_type'])
            ->map(function (PaymentRequest $pr): array {
                $at = $pr->status === PaymentRequest::STATUS_APPROVED && $pr->reviewed_at !== null
                    ? $pr->reviewed_at
                    : $pr->created_at;
                $dateStr = $at ? Carbon::parse($at)->format('d-M-Y') : '';
                $ref = trim((string) ($pr->request_no ?? ''));
                $typeLabel = PaymentRequest::typeLabel((string) $pr->payment_type);
                $caption = $pr->status === PaymentRequest::STATUS_APPROVED
                    ? 'Paid on '.$dateStr.' · '.$typeLabel
                    : 'Pending since '.$dateStr.' · '.$typeLabel;
                if ($ref !== '') {
                    $caption .= ' · '.$ref;
                }
                $ts = $at ? (float) Carbon::parse($at)->timestamp : 0.0;
                $ts += ((int) $pr->id) / 1_000_000;

                return [
                    'date' => $dateStr,
                    'amount' => (float) $pr->amount,
                    'caption' => $caption,
                    '_ts' => $ts,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * PO “against” history: vendor bill pay lines for bills on this PO + payment requests not double-counted with those lines.
     *
     * @return array<int, array{date: string, amount: float, caption: string}>
     */
    private function purchaseOrderPastPaymentHistoryMerged(int $poId): array
    {
        $merged = array_merge(
            $this->billPayLinesHistoryForPurchaseOrder($poId),
            $this->paymentRequestHistoryForPurchaseOrderNonBillBacked($poId),
        );

        usort($merged, static function (array $a, array $b): int {
            $ta = $a['_ts'] ?? 0.0;
            $tb = $b['_ts'] ?? 0.0;
            if (abs($ta - $tb) > 0.000001) {
                return $ta <=> $tb;
            }

            return 0;
        });

        return array_map(static function (array $row): array {
            unset($row['_ts']);

            return $row;
        }, $merged);
    }

    /**
     * Total paid / pending against a PO using merged history (same basis as {@see purchaseOrderPastPaymentHistoryMerged}).
     */
    private function purchaseOrderMergedHistoryPaidTotal(int $poId): float
    {
        $merged = $this->purchaseOrderPastPaymentHistoryMerged($poId);

        return round(array_sum(array_column($merged, 'amount')), 2);
    }

    /**
     * Whether this request’s amount is counted in the payment-request slice of merged PO history
     * (pending always; approved only when not yet represented by a vendor bill line — either via
     * payment_requests.bill_id or via bill_tbl.payment_request_id link mode).
     */
    private function paymentRequestIncludedInPoMergedPrHistory(PaymentRequest $pr): bool
    {
        if (! $pr->purchase_order_id) {
            return false;
        }
        if ($pr->status === PaymentRequest::STATUS_PENDING) {
            return true;
        }
        if ($pr->status === PaymentRequest::STATUS_APPROVED) {
            $hasForwardBill = (int) ($pr->bill_id ?? 0) > 0;
            if ($hasForwardBill) {
                return false;
            }
            $hasReverseBill = $pr->linkedBills()->exists();

            return ! $hasReverseBill;
        }

        return false;
    }

    /**
     * Remaining amount that may still be allocated on this PO (merged bill + PR activity vs PO grand total).
     *
     * @param  int|null  $excludePaymentRequestId  When updating, treat this row’s merged PR slice as removable headroom.
     */
    private function remainingOnPurchaseOrderAgainstMergedHistory(TblPurchaseorder $po, ?int $excludePaymentRequestId): float
    {
        $poTotal = max(0.0, (float) ($po->grand_total_amount ?? 0));
        $merged = $this->purchaseOrderMergedHistoryPaidTotal((int) $po->id);
        if ($excludePaymentRequestId !== null) {
            $ex = PaymentRequest::query()->find($excludePaymentRequestId);
            if ($ex && $this->paymentRequestIncludedInPoMergedPrHistory($ex) && (int) $ex->purchase_order_id === (int) $po->id) {
                $merged = round($merged - (float) $ex->amount, 2);
            }
        }

        return round(max(0.0, $poTotal - $merged), 2);
    }

    /**
     * Payment-request aggregates for a vendor bill (Part Payment / Settlement).
     *
     * @return array{sum_pending_and_approved: float, sum_approved_only: float, last_approved_amount: float}
     */
    private function paymentRequestTotalsForBill(int $billId): array
    {
        $sumPendingAndApproved = (float) PaymentRequest::query()
            ->where('bill_id', $billId)
            ->countingTowardBill()
            ->sum('amount');
        $sumApproved = (float) PaymentRequest::query()
            ->where('bill_id', $billId)
            ->where('status', PaymentRequest::STATUS_APPROVED)
            ->sum('amount');
        $last = PaymentRequest::query()
            ->where('bill_id', $billId)
            ->where('status', PaymentRequest::STATUS_APPROVED)
            ->orderByDesc('id')
            ->value('amount');
        $lastAmt = $last !== null ? (float) $last : 0.0;

        return [
            'sum_pending_and_approved' => $sumPendingAndApproved,
            'sum_approved_only' => $sumApproved,
            'last_approved_amount' => $lastAmt,
        ];
    }

    /**
     * Payment requests raised against this bill (pending + approved), for the create form “Bill against” panel.
     *
     * @return array<int, array{date: string, amount: float, caption: string}>
     */
    private function paymentRequestHistoryForBill(int $billId): array
    {
        $billRefRow = Tblbill::query()
            ->whereKey($billId)
            ->first(['bill_gen_number', 'bill_number']);
        $billRefLabel = $billRefRow
            ? trim((string) ($billRefRow->bill_gen_number ?: $billRefRow->bill_number ?: ''))
            : '';

        return PaymentRequest::query()
            ->where('bill_id', $billId)
            ->countingTowardBill()
            ->orderByRaw('COALESCE(reviewed_at, created_at) ASC')
            ->orderBy('id')
            ->get(['id', 'amount', 'status', 'created_at', 'reviewed_at', 'request_no', 'payment_type'])
            ->map(function (PaymentRequest $pr) use ($billRefLabel): array {
                $at = $pr->status === PaymentRequest::STATUS_APPROVED && $pr->reviewed_at !== null
                    ? $pr->reviewed_at
                    : $pr->created_at;
                $dateStr = $at ? Carbon::parse($at)->format('d-M-Y') : '';
                $ref = trim((string) ($pr->request_no ?? ''));
                $typeLabel = PaymentRequest::typeLabel((string) $pr->payment_type);
                $caption = $pr->status === PaymentRequest::STATUS_APPROVED
                    ? 'Approved on '.$dateStr.' · '.$typeLabel.' (Payment request)'
                    : 'Pending since '.$dateStr.' · '.$typeLabel.' (Payment request)';
                if ($ref !== '') {
                    $caption .= ' · '.$ref;
                }

                return [
                    'date' => $dateStr,
                    'amount' => (float) $pr->amount,
                    'caption' => $caption,
                    'status' => (string) $pr->status,
                    'status_label' => PaymentRequest::statusLabel((string) $pr->status),
                    'type_label' => $typeLabel,
                    'ref' => $ref,
                    'anchor' => 'bill',
                    'anchor_label' => $billRefLabel !== '' ? 'Against '.$billRefLabel : 'Against bill',
                ];
            })
            ->values()
            ->all();
    }

    /**
     * All payment requests on this PO (pending + approved), for show-page breakdown (same idea as bill PR list).
     *
     * @return array<int, array{date: string, amount: float, caption: string}>
     */
    private function paymentRequestHistoryForPurchaseOrderAll(int $poId): array
    {
        $rows = PaymentRequest::query()
            ->where('purchase_order_id', $poId)
            ->countingTowardPo()
            ->with([
                'sourceBill:id,bill_gen_number,bill_number,delete_status',
                'linkedBills:id,payment_request_id,bill_gen_number,bill_number,delete_status,bill_pr_link_mode',
            ])
            ->orderByRaw('COALESCE(reviewed_at, created_at) ASC')
            ->orderBy('id')
            ->get(['id', 'amount', 'status', 'created_at', 'reviewed_at', 'request_no', 'payment_type', 'bill_id', 'purchase_order_id']);

        return $rows->map(function (PaymentRequest $pr): array {
            $at = $pr->status === PaymentRequest::STATUS_APPROVED && $pr->reviewed_at !== null
                ? $pr->reviewed_at
                : $pr->created_at;
            $dateStr = $at ? Carbon::parse($at)->format('d-M-Y') : '';
            $ref = trim((string) ($pr->request_no ?? ''));
            $typeLabel = PaymentRequest::typeLabel((string) $pr->payment_type);
            $caption = $pr->status === PaymentRequest::STATUS_APPROVED
                ? 'Approved on '.$dateStr.' · '.$typeLabel.' (Payment request)'
                : 'Pending since '.$dateStr.' · '.$typeLabel.' (Payment request)';
            if ($ref !== '') {
                $caption .= ' · '.$ref;
            }

            $anchor = 'po';
            $anchorLabel = 'Against PO';
            if ($pr->sourceBill && (int) ($pr->sourceBill->delete_status ?? 0) === 0) {
                $anchor = 'bill';
                $billRef = trim((string) ($pr->sourceBill->bill_gen_number ?: $pr->sourceBill->bill_number ?: ''));
                $anchorLabel = $billRef !== '' ? 'Against '.$billRef : 'Against bill';
            } else {
                $linked = $pr->linkedBills->first(static function ($b) {
                    return (int) ($b->delete_status ?? 0) === 0;
                });
                if ($linked) {
                    $anchor = 'bill';
                    $billRef = trim((string) ($linked->bill_gen_number ?: $linked->bill_number ?: ''));
                    $anchorLabel = $billRef !== '' ? 'Bill raised: '.$billRef : 'Bill raised against this PR';
                }
            }

            return [
                'date' => $dateStr,
                'amount' => (float) $pr->amount,
                'caption' => $caption,
                'status' => (string) $pr->status,
                'status_label' => PaymentRequest::statusLabel((string) $pr->status),
                'type_label' => $typeLabel,
                'ref' => $ref,
                'anchor' => $anchor,
                'anchor_label' => $anchorLabel,
            ];
        })
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{date: string, amount: float, caption: string}>
     */
    private function billPayLineRowsForPurchaseOrderDisplay(int $poId): array
    {
        return array_values(array_map(static function (array $row): array {
            return [
                'date' => $row['date'] ?? '',
                'amount' => (float) ($row['amount'] ?? 0),
                'caption' => 'Bill / NEFT payment',
            ];
        }, $this->billPayLinesHistoryForPurchaseOrder($poId)));
    }

    /**
     * Live bill figures: reconciles grand_total − partially_payment with balance_amount when they drift,
     * and exposes headroom for new / pending payment requests (Bill Made + approved PRs live in partially_payment).
     *
     * @return array{
     *     grand: float,
     *     balance_db: float,
     *     partial: float,
     *     reconciled_remaining: float,
     *     pending_pr: float,
     *     payable: float,
     *     paid_outside_requests: float
     * }
     */
    private function billFinancialSnapshot(Tblbill $bill): array
    {
        $eps = 0.02;
        $grand = max(0.0, (float) ($bill->grand_total_amount ?? 0));
        $dbBal = max(0.0, (float) ($bill->balance_amount ?? 0));
        $partial = max(0.0, (float) ($bill->partially_payment ?? 0));
        $fromPartial = max(0.0, $grand - $partial);
        $reconciled = abs($dbBal - $fromPartial) <= $eps ? $dbBal : $fromPartial;
        $pendingPr = (float) PaymentRequest::query()
            ->where('bill_id', (int) $bill->id)
            ->where('status', PaymentRequest::STATUS_PENDING)
            ->sum('amount');
        $approvedPr = (float) PaymentRequest::query()
            ->where('bill_id', (int) $bill->id)
            ->where('status', PaymentRequest::STATUS_APPROVED)
            ->sum('amount');
        $payable = max(0.0, $reconciled - $pendingPr);
        $paidOutsidePr = max(0.0, $partial - $approvedPr);

        return [
            'grand' => $grand,
            'balance_db' => $dbBal,
            'partial' => $partial,
            'reconciled_remaining' => $reconciled,
            'pending_pr' => $pendingPr,
            'payable' => $payable,
            'paid_outside_requests' => $paidOutsidePr,
        ];
    }

    public function index(Request $request): View
    {
        $u = $this->userRow();
        $loc = $this->locationDropdownData();
        $vendors = Tblvendor::query()
            ->where('active_status', 0)
            ->orderBy('display_name')
            ->orderBy('company_name')
            ->get(['id', 'display_name', 'company_name', 'vendor_id']);

        $query = PaymentRequest::query()
            ->with([
                'branch:id,name',
                'company:id,company_name',
                'zone:id,name',
                'creator:id,user_fullname',
                'sourceVendor:id,display_name,company_name,vendor_id',
                'linkedBills:id,payment_request_id,grand_total_amount,balance_amount,bill_gen_number,bill_number,delete_status',
            ]);

        $this->scopePaymentRequestsForUser($query, $u);

        if ($request->filled('date_from')) {
            try {
                $query->whereDate('created_at', '>=', Carbon::parse($request->date_from)->startOfDay());
            } catch (\Throwable $e) {
            }
        }
        if ($request->filled('date_to')) {
            try {
                $query->whereDate('created_at', '<=', Carbon::parse($request->date_to)->endOfDay());
            } catch (\Throwable $e) {
            }
        }

        $companyIds = array_values(array_filter(array_map('intval', (array) $request->input('company_id', []))));
        if ($companyIds !== []) {
            $query->whereIn('company_id', $companyIds);
        }

        $zoneIds = array_values(array_filter(array_map('intval', (array) $request->input('zone_id', []))));
        if ($zoneIds !== []) {
            $query->whereIn('zone_id', $zoneIds);
        }

        $branchIds = array_values(array_filter(array_map('intval', (array) $request->input('branch_id', []))));
        if ($branchIds !== []) {
            $query->whereIn('branch_id', $branchIds);
        }

        $paymentTypes = array_values(array_filter((array) $request->input('payment_type', [])));
        if ($paymentTypes !== []) {
            $paymentTypes = array_values(array_intersect($paymentTypes, PaymentRequest::TYPES));
            if ($paymentTypes !== []) {
                $query->whereIn('payment_type', $paymentTypes);
            }
        }

        $statuses = array_values(array_filter((array) $request->input('status', [])));
        if ($statuses !== []) {
            $statuses = array_values(array_intersect($statuses, PaymentRequest::STATUSES));
            if ($statuses !== []) {
                $query->whereIn('status', $statuses);
            }
        }

        $vendorIds = array_values(array_filter(array_map('intval', (array) $request->input('vendor_id', []))));
        if ($vendorIds !== []) {
            $query->whereIn('vendor_id', $vendorIds);
        }

        if ($request->filled('universal_search')) {
            $term = Str::limit(trim((string) $request->input('universal_search', '')), 200, '');
            if ($term !== '') {
                $like = '%'.addcslashes($term, '%_\\').'%';
                $query->where(function (Builder $q) use ($like) {
                    $q->where('request_no', 'like', $like)
                        ->orWhere('remarks', 'like', $like)
                        ->orWhere('rejection_reason', 'like', $like)
                        ->orWhere('bank_account_number', 'like', $like)
                        ->orWhere('bank_ifsc_code', 'like', $like)
                        ->orWhere('bank_branch_details', 'like', $like)
                        ->orWhere('payment_type', 'like', $like)
                        ->orWhere('status', 'like', $like)
                        ->orWhereRaw('CAST(amount AS CHAR) LIKE ?', [$like])
                        ->orWhereHas('creator', static function (Builder $sub) use ($like) {
                            $sub->where('user_fullname', 'like', $like);
                        })
                        ->orWhereHas('zone', static function (Builder $sub) use ($like) {
                            $sub->where('name', 'like', $like);
                        })
                        ->orWhereHas('branch', static function (Builder $sub) use ($like) {
                            $sub->where('name', 'like', $like);
                        })
                        ->orWhereHas('company', static function (Builder $sub) use ($like) {
                            $sub->where('company_name', 'like', $like);
                        })
                        ->orWhereHas('legacyPurchaseOrder', static function (Builder $sub) use ($like) {
                            $sub->where('purchase_gen_order', 'like', $like);
                        })
                        ->orWhereHas('linkedBills', static function (Builder $sub) use ($like) {
                            $sub->where('bill_number', 'like', $like)
                                ->orWhere('bill_gen_number', 'like', $like);
                        });
                });
            }
        }

        $statsBase = clone $query;
        $startMonth = now()->startOfMonth();

        $stats = [
            'total' => (int) (clone $statsBase)->count(),
            'sum_amount' => (float) (clone $statsBase)->sum('amount'),
            'this_month' => (int) (clone $statsBase)->where('created_at', '>=', $startMonth)->count(),
            'po_linked' => (int) (clone $statsBase)->whereNotNull('purchase_order_id')->count(),
        ];

        $perPageChoices = [10, 15, 25, 50, 100];
        $perPage = (int) $request->query('per_page', 10);
        if (! in_array($perPage, $perPageChoices, true)) {
            $perPage = 10;
        }

        $rows = (clone $query)->orderByDesc('id')->paginate($perPage)->withQueryString();

        return view('superadmin.payment_requests.index', [
            'admin' => $u,
            'rows' => $rows,
            'pr_per_page' => $perPage,
            'pr_per_page_choices' => $perPageChoices,
            'stats' => $stats,
            'companies' => $loc['companies'],
            'zones' => $loc['zones'],
            'branches' => $loc['branches'],
            'vendors' => $vendors,
            'paymentTypeLabels' => PaymentRequest::TYPE_LABELS,
            'statusLabels' => PaymentRequest::STATUS_LABELS,
            'paymentRequestListScopedToSelf' => ! $this->isPaymentRequestSuperAdmin($u),
        ]);
    }

    public function create(Request $request): View
    {
        $u = $this->userRow();
        $loc = $this->locationDropdownData();
        $vendors = Tblvendor::query()
            ->where('active_status', 0)
            ->orderBy('display_name')
            ->orderBy('company_name')
            ->get(['id', 'display_name', 'company_name', 'vendor_id']);

        return view('superadmin.payment_requests.create', [
            'admin' => $u,
            'companies' => $loc['companies'],
            'zones' => $loc['zones'],
            'branches' => $loc['branches'],
            'vendors' => $vendors,
            'branchFetchUrl' => route('superadmin.getbranchfetch'),
            'lookupBillUrl' => route('superadmin.payment-requests.lookup-bill'),
        ]);
    }

    /**
     * PO snapshot for linked payment types: grand total, prior paid in this module, etc.
     */
    public function lookupPo(Request $request): JsonResponse
    {
        $this->userRow();
        $g = trim((string) $request->input('purchase_gen_order', $request->query('purchase_gen_order', '')));
        if ($g === '') {
            return response()->json([
                'ok' => false,
                'message' => 'Enter the generated PO number (purchase_gen_order).',
            ], 422);
        }
        if (strlen($g) > 100) {
            return response()->json([
                'ok' => false,
                'message' => 'PO number is too long.',
            ], 422);
        }
        $po = TblPurchaseorder::query()->where('purchase_gen_order', $g)->first();
        if (! $po) {
            return response()->json([
                'ok' => false,
                'message' => 'No purchase order found for this number.',
            ], 404);
        }
        $poTotal = (float) ($po->grand_total_amount ?? 0);
        $prTotals = $this->paymentRequestTotalsForPurchaseOrder((int) $po->id);
        $paidBefore = $prTotals['sum_pending_and_approved'];
        $vendorName = (string) ($po->vendor_name ?? '');
        if ($vendorName === '' && $po->vendor_id) {
            $v = Tblvendor::query()
                ->where(function ($q) use ($po) {
                    $q->where('id', $po->vendor_id)
                        ->orWhere('vendor_id', $po->vendor_id);
                })
                ->first();
            if ($v) {
                $vendorName = (string) ($v->display_name ?: $v->company_name);
            }
        }

        $poPast = $this->purchaseOrderPastPaymentHistoryMerged((int) $po->id);
        $poHistoryPaid = round(array_sum(array_column($poPast, 'amount')), 2);

        return response()->json([
            'ok' => true,
            'purchase_order_id' => (int) $po->id,
            'purchase_gen_order' => (string) ($po->purchase_gen_order ?? ''),
            'order_number' => (string) ($po->order_number ?? $po->purchase_order_number ?? $po->purchase_gen_order ?? ''),
            'po_total' => $poTotal,
            'amount_paid_before' => $paidBefore,
            'amount_paid_approved_only' => $prTotals['sum_approved_only'],
            'last_approved_payment_amount' => $prTotals['last_approved_amount'],
            'remaining_before_new' => max(0, $poTotal - $paidBefore),
            'vendor_name' => $vendorName,
            'vendor_id' => $po->vendor_id,
            'company_name' => (string) ($po->company_name ?? ''),
            'zone_name' => (string) ($po->zone_name ?? ''),
            'branch_name' => (string) ($po->branch_name ?? ''),
            'po_pdf_url' => route('superadmin.getpurchasepdf', ['id' => $po->id]),
            'po_past_payments' => $poPast,
            /** Sum of merged PO-against lines (Bill Made / NEFT on PO bills + payment requests not on a bill line). */
            'po_history_paid_total' => $poHistoryPaid,
            'po_history_remaining' => round(max(0.0, $poTotal - $poHistoryPaid), 2),
        ]);
    }

    /**
     * Bill snapshot for PO-linked payment types: location, vendor, optional PO totals from the bill's purchase_id.
     */
    public function lookupBill(Request $request): JsonResponse
    {
        $this->userRow();
        $ref = trim((string) $request->input('bill_ref', $request->query('bill_ref', '')));
        if ($ref === '') {
            return response()->json([
                'ok' => false,
                'message' => 'Enter a bill number or generated bill reference.',
            ], 422);
        }
        if (strlen($ref) > 120) {
            return response()->json([
                'ok' => false,
                'message' => 'Bill reference is too long.',
            ], 422);
        }

        $bill = Tblbill::query()
            ->where(function ($q) {
                $q->where('delete_status', 0)->orWhereNull('delete_status');
            })
            ->where(function ($q) use ($ref) {
                $q->where('bill_gen_number', $ref)
                    ->orWhere('bill_number', $ref)
                    ->orWhereRaw('LOWER(TRIM(bill_gen_number)) = LOWER(?)', [$ref])
                    ->orWhereRaw('LOWER(TRIM(bill_number)) = LOWER(?)', [$ref]);
            })
            ->orderByDesc('id')
            ->first();

        if (! $bill) {
            return response()->json([
                'ok' => false,
                'message' => 'No active bill found for this number.',
            ], 404);
        }

        $vendorTblId = $this->resolveVendorTableIdForBill($bill);
        $vendorLabel = '';
        if ($vendorTblId) {
            $v = Tblvendor::query()->find($vendorTblId);
            if ($v) {
                $vendorLabel = trim((string) ($v->display_name ?: $v->company_name));
            }
        }
        if ($vendorLabel === '') {
            $vendorLabel = trim((string) ($bill->vendor_name ?? ''));
        }

        $billGrand = (float) ($bill->grand_total_amount ?? 0);
        $billBal = (float) ($bill->balance_amount ?? 0);
        $billPr = $this->paymentRequestTotalsForBill((int) $bill->id);
        $snap = $this->billFinancialSnapshot($bill);
        $billPaidDerived = max(0.0, $billGrand - $snap['reconciled_remaining']);
        $billRemainingPayable = $snap['payable'];

        $pastPayments = TblBillPayLines::query()
            ->where('bill_id', $bill->id)
            ->orderBy('id', 'asc')
            ->get(['payment_date', 'amount', 'id'])
            ->map(function ($pay) {
                return [
                    'date' => $this->formatBillPayLineDateForDisplay($pay->payment_date ?? null),
                    'amount' => (float) $pay->amount,
                ];
            })
            ->toArray();

        $payload = [
            'ok' => true,
            'bill_id' => (int) $bill->id,
            'bill_gen_number' => (string) ($bill->bill_gen_number ?? ''),
            'bill_number' => (string) ($bill->bill_number ?? ''),
            'bill_grand_total' => $billGrand,
            'bill_balance' => $billBal,
            /** Remaining on the bill after Bill Made / approved PRs; uses reconciled figures when balance_amount drifts. */
            'bill_remaining_payable' => $billRemainingPayable,
            'bill_paid_outside_requests' => $snap['paid_outside_requests'],
            'bill_pending_requests_total' => $snap['pending_pr'],
            /** Settled payments on the bill (Bill Made / bank + approved PRs), from reconciled remainder. */
            'bill_paid_derived' => $billPaidDerived,
            'bill_previously_paid_total' => $billPaidDerived,
            'bill_past_payments' => $pastPayments,
            'bill_payment_request_history' => $this->paymentRequestHistoryForBill((int) $bill->id),
            'bill_last_approved_payment' => $billPr['last_approved_amount'],
            'bill_sum_approved_requests' => $billPr['sum_approved_only'],
            'bill_sum_pending_and_approved_requests' => $billPr['sum_pending_and_approved'],
            'company_id' => $bill->company_id ? (int) $bill->company_id : null,
            'company_name' => (string) ($bill->company_name ?? ''),
            'zone_id' => $bill->zone_id ? (int) $bill->zone_id : null,
            'zone_name' => (string) ($bill->zone_name ?? ''),
            'branch_id' => $bill->branch_id ? (int) $bill->branch_id : null,
            'branch_name' => (string) ($bill->branch_name ?? ''),
            'vendor_id' => $vendorTblId,
            'vendor_name' => $vendorLabel,
            'has_po' => false,
        ];

        $purchaseId = $bill->purchase_id ? (int) $bill->purchase_id : 0;
        if ($purchaseId > 0) {
            $po = TblPurchaseorder::query()
                ->where('delete_status', 0)
                ->where('id', $purchaseId)
                ->first();
            if ($po) {
                $poTotal = (float) ($po->grand_total_amount ?? 0);
                $poPr = $this->paymentRequestTotalsForPurchaseOrder((int) $po->id);
                $paidBefore = $poPr['sum_pending_and_approved'];
                $payload['has_po'] = true;
                $payload['purchase_order_id'] = (int) $po->id;
                $payload['purchase_gen_order'] = (string) ($po->purchase_gen_order ?? '');
                $payload['order_number'] = (string) ($po->order_number ?? $po->purchase_order_number ?? $po->purchase_gen_order ?? '');
                $payload['po_total'] = $poTotal;
                $payload['amount_paid_before'] = $paidBefore;
                $payload['amount_paid_approved_only'] = $poPr['sum_approved_only'];
                $payload['last_approved_payment_amount'] = $poPr['last_approved_amount'];
                $payload['remaining_before_new'] = max(0, $poTotal - $paidBefore);
                $payload['po_pdf_url'] = route('superadmin.getpurchasepdf', ['id' => $po->id]);
                $poPast = $this->purchaseOrderPastPaymentHistoryMerged((int) $po->id);
                $poHistoryPaid = round(array_sum(array_column($poPast, 'amount')), 2);
                $payload['po_past_payments'] = $poPast;
                $payload['po_history_paid_total'] = $poHistoryPaid;
                $payload['po_history_remaining'] = round(max(0.0, $poTotal - $poHistoryPaid), 2);
                $payload['po_company_name'] = (string) ($po->company_name ?? '');
                $payload['po_zone_name'] = (string) ($po->zone_name ?? '');
                $payload['po_branch_name'] = (string) ($po->branch_name ?? '');
                $poVendorName = (string) ($po->vendor_name ?? '');
                if ($poVendorName === '' && $po->vendor_id) {
                    $pv = Tblvendor::query()
                        ->where(function ($q) use ($po) {
                            $q->where('id', $po->vendor_id)
                                ->orWhere('vendor_id', $po->vendor_id);
                        })
                        ->first();
                    if ($pv) {
                        $poVendorName = trim((string) ($pv->display_name ?: $pv->company_name));
                    }
                }
                $payload['po_vendor_name'] = $poVendorName;
            }
        }

        return response()->json($payload);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $this->userRow();
        $type = (string) $request->input('payment_type', '');

        $base = [
            'company_id' => 'required|integer|exists:company_tbl,id',
            'zone_id' => 'required|integer|exists:tblzones,id',
            'branch_id' => 'required|integer|exists:tbl_locations,id',
            'vendor_id' => 'required|integer|exists:vendor_tbl,id',
            'payment_type' => ['required', 'string', Rule::in(PaymentRequest::TYPES)],
            'amount' => 'required|numeric|min:0.01',
            'remarks' => 'nullable|string|max:10000',
            'bill_id' => 'nullable|integer|exists:bill_tbl,id',
        ];

        if (PaymentRequest::requiresPoAttachment($type)) {
            $base['po_link_mode'] = ['required', 'string', Rule::in(['po', 'bill'])];
            $base['purchase_gen_order'] = 'nullable|string|max:100';
            $base['po_attachment'] = 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx';
        } else {
            $base['document_attachment'] = 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx';
        }

        if (PaymentRequest::requiresPayeeBankDetails($type)) {
            $base['bank_account_number'] = 'required|string|max:64';
            $base['bank_ifsc_code'] = ['required', 'string', 'size:11', 'regex:/^[A-Za-z]{4}0[A-Za-z0-9]{6}$/'];
            $base['bank_branch_details'] = 'required|string|max:5000';
            $base['bank_document'] = 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx';
        }

        $validated = $request->validate(CreateFormDuration::mergeRules($base));

        $okBranch = TblLocationModel::query()
            ->where('id', (int) $validated['branch_id'])
            ->where('zone_id', (int) $validated['zone_id'])
            ->exists();
        if (! $okBranch) {
            throw ValidationException::withMessages([
                'branch_id' => 'The selected branch must belong to the selected zone.',
            ]);
        }

        $linkMode = PaymentRequest::requiresPoAttachment($type)
            ? (string) ($validated['po_link_mode'] ?? 'po')
            : 'po';

        if ($type === PaymentRequest::TYPE_ADVANCE) {
            $linkMode = 'po';
        }
        if ($type === PaymentRequest::TYPE_SETTLEMENT) {
            $linkMode = 'bill';
        }

        $linkedBill = null;
        if (PaymentRequest::requiresPoAttachment($type) && $linkMode === 'po' && ! empty($validated['bill_id'])) {
            $msg = $type === PaymentRequest::TYPE_ADVANCE
                ? 'Advance is linked by PO only. Remove the bill reference.'
                : 'You chose “Against purchase order (PO)”. Clear the bill or switch to “Against vendor bill”.';
            throw ValidationException::withMessages([
                'po_link_mode' => $msg,
            ]);
        }
        if (PaymentRequest::requiresPoAttachment($type) && $linkMode === 'bill' && empty($validated['bill_id'])) {
            $billMsg = $type === PaymentRequest::TYPE_SETTLEMENT
                ? 'Settlement is against a vendor bill. Enter the bill number, tap Load, then submit.'
                : 'Choose “Against vendor bill”, enter the bill reference, tap Load, then submit.';
            throw ValidationException::withMessages([
                'bill_id' => $billMsg,
            ]);
        }
        $linkedBillFinancial = null;
        if (! empty($validated['bill_id'])) {
            $linkedBill = Tblbill::query()
                ->where('id', (int) $validated['bill_id'])
                ->where(function ($q) {
                    $q->where('delete_status', 0)->orWhereNull('delete_status');
                })
                ->first();
            if (! $linkedBill) {
                throw ValidationException::withMessages([
                    'bill_id' => 'Bill not found or is not active.',
                ]);
            }
            $billVendorTblId = $this->resolveVendorTableIdForBill($linkedBill);
            if ($billVendorTblId && (int) $validated['vendor_id'] !== $billVendorTblId) {
                throw ValidationException::withMessages([
                    'vendor_id' => 'Vendor must match the vendor on the selected bill.',
                ]);
            }
            $linkedBillFinancial = $this->billFinancialSnapshot($linkedBill);
            $reqAmt = (float) $validated['amount'];
            if ($reqAmt - $linkedBillFinancial['payable'] > 0.02) {
                throw ValidationException::withMessages([
                    'amount' => 'This amount (₹'.number_format($reqAmt, 2)
                        .') is more than the balance left on the vendor bill (₹'.number_format($linkedBillFinancial['payable'], 2)
                        .'). That limit includes Bill Made / bank payments and other pending payment requests on this bill.',
                ]);
            }
        }

        $po = null;
        if (PaymentRequest::requiresPoAttachment($type)) {
            if ($linkMode === 'bill') {
                if ($linkedBill && $linkedBill->purchase_id) {
                    $po = TblPurchaseorder::query()
                        ->where('delete_status', 0)
                        ->where('id', (int) $linkedBill->purchase_id)
                        ->first();
                    if (! $po) {
                        throw ValidationException::withMessages([
                            'bill_id' => 'The bill is linked to a purchase order that could not be found.',
                        ]);
                    }
                }
            } else {
                $g = trim((string) ($validated['purchase_gen_order'] ?? ''));
                if ($g === '') {
                    throw ValidationException::withMessages([
                        'purchase_gen_order' => 'Enter the PO number (purchase_gen_order) or switch to Vendor bill.',
                    ]);
                }
                $po = TblPurchaseorder::query()->where('purchase_gen_order', $g)->first();
                if (! $po) {
                    throw ValidationException::withMessages([
                        'purchase_gen_order' => 'No purchase order found for this PO number (purchase_gen_order).',
                    ]);
                }
            }

            if ($po) {
                $poTotal = (float) ($po->grand_total_amount ?? 0);
                if ($poTotal <= 0) {
                    $errKey = $linkMode === 'bill' ? 'bill_id' : 'purchase_gen_order';
                    throw ValidationException::withMessages([
                        $errKey => 'Selected PO has no valid grand total on file.',
                    ]);
                }
                $remainingPo = $this->remainingOnPurchaseOrderAgainstMergedHistory($po, null);
                $thisAmount = (float) $validated['amount'];
                if ($thisAmount - $remainingPo > 0.02) {
                    throw ValidationException::withMessages([
                        'amount' => 'This amount (₹'.number_format($thisAmount, 2)
                            .') is more than the remaining headroom on this purchase order (₹'.number_format($remainingPo, 2)
                            .'). That limit includes vendor bill / NEFT payments and other payment requests already against this PO.',
                    ]);
                }
            }
        }

        $poPath = null;
        $docPath = null;
        $bankDocPath = null;
        if (PaymentRequest::requiresPoAttachment($type)) {
            $poPath = $this->saveUploaded($request->file('po_attachment'), 'superadmin/payment_requests');
        } else {
            $docPath = $this->saveUploaded($request->file('document_attachment'), 'superadmin/payment_requests');
        }
        if (PaymentRequest::requiresPayeeBankDetails($type)) {
            $bankDocPath = $this->saveUploaded($request->file('bank_document'), 'superadmin/payment_requests');
        }

        $finalVendorId = $validated['vendor_id'] ?? null;
        if (empty($finalVendorId) && $po && $po->vendor_id) {
            $finalVendorId = $po->vendor_id;
        }

        $created = PaymentRequest::create([
            'request_no' => $this->nextRequestNumber(),
            'company_id' => $validated['company_id'] ?? null,
            'zone_id' => (int) $validated['zone_id'],
            'branch_id' => (int) $validated['branch_id'],
            'vendor_id' => $finalVendorId,
            'payment_type' => $type,
            'amount' => $validated['amount'],
            'purchase_order_id' => $po?->id,
            'bill_id' => PaymentRequest::requiresPoAttachment($type)
                ? (($linkMode === 'bill' && $linkedBill) ? $linkedBill->id : null)
                : ($linkedBill?->id),
            'bill_total_snapshot' => $linkedBill ? (float) ($linkedBill->grand_total_amount ?? 0) : null,
            'bill_balance_snapshot' => $linkedBillFinancial ? $linkedBillFinancial['reconciled_remaining'] : null,
            'po_total_snapshot' => $po ? (float) ($po->grand_total_amount ?? 0) : null,
            'po_attachment_path' => $poPath,
            'document_attachment_path' => $docPath,
            'bank_account_number' => $validated['bank_account_number'] ?? null,
            'bank_ifsc_code' => isset($validated['bank_ifsc_code']) ? strtoupper($validated['bank_ifsc_code']) : null,
            'bank_branch_details' => $validated['bank_branch_details'] ?? null,
            'bank_document_path' => $bankDocPath,
            'remarks' => $validated['remarks'] ?? null,
            'status' => PaymentRequest::STATUS_PENDING,
            'created_by' => (int) auth()->id(),
        ]);

        $successMessage = 'Payment request submitted successfully.';
        if ($request->expectsJson()) {
            session()->flash('success', $successMessage);

            return response()->json([
                'ok' => true,
                'redirect' => route('superadmin.payment-requests.index', $created),
            ]);
        }

        return redirect()
            ->route('superadmin.payment-requests.index', $created)
            ->with('success', $successMessage);
    }

    /**
     * Only the creator (or a super-admin) may edit; only while the request is still pending review.
     */
    private function authorizePaymentRequestEdit(PaymentRequest $paymentRequest): void
    {
        $u = $this->userRow();
        if (! $this->isPaymentRequestSuperAdmin($u)) {
            if ((int) $paymentRequest->created_by !== (int) auth()->id()) {
                abort(403, 'You can only edit payment requests you created.');
            }
        }
        if (! $paymentRequest->isPendingReview()) {
            abort(403, 'This payment request is no longer pending review and cannot be edited.');
        }
    }

    public function edit(PaymentRequest $paymentRequest): View
    {
        $this->authorizePaymentRequestEdit($paymentRequest);

        $paymentRequest->load([
            'legacyPurchaseOrder:id,purchase_gen_order',
            'sourceBill:id,bill_gen_number,bill_number',
        ]);

        $u = $this->userRow();
        $loc = $this->locationDropdownData();
        $vendors = Tblvendor::query()
            ->where('active_status', 0)
            ->orderBy('display_name')
            ->orderBy('company_name')
            ->get(['id', 'display_name', 'company_name', 'vendor_id']);

        return view('superadmin.payment_requests.create', [
            'admin' => $u,
            'companies' => $loc['companies'],
            'zones' => $loc['zones'],
            'branches' => $loc['branches'],
            'vendors' => $vendors,
            'branchFetchUrl' => route('superadmin.getbranchfetch'),
            'lookupBillUrl' => route('superadmin.payment-requests.lookup-bill'),
            'paymentRequest' => $paymentRequest,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, PaymentRequest $paymentRequest): JsonResponse|RedirectResponse
    {
        $this->authorizePaymentRequestEdit($paymentRequest);

        $type = (string) $request->input('payment_type', '');

        $base = [
            'company_id' => 'required|integer|exists:company_tbl,id',
            'zone_id' => 'required|integer|exists:tblzones,id',
            'branch_id' => 'required|integer|exists:tbl_locations,id',
            'vendor_id' => 'required|integer|exists:vendor_tbl,id',
            'payment_type' => ['required', 'string', Rule::in(PaymentRequest::TYPES)],
            'amount' => 'required|numeric|min:0.01',
            'remarks' => 'nullable|string|max:10000',
            'bill_id' => 'nullable|integer|exists:bill_tbl,id',
        ];

        if (PaymentRequest::requiresPoAttachment($type)) {
            $base['po_link_mode'] = ['required', 'string', Rule::in(['po', 'bill'])];
            $base['purchase_gen_order'] = 'nullable|string|max:100';
            $base['po_attachment'] = 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx';
        } else {
            $base['document_attachment'] = 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx';
        }

        if (PaymentRequest::requiresPayeeBankDetails($type)) {
            $base['bank_account_number'] = 'required|string|max:64';
            $base['bank_ifsc_code'] = ['required', 'string', 'size:11', 'regex:/^[A-Za-z]{4}0[A-Za-z0-9]{6}$/'];
            $base['bank_branch_details'] = 'required|string|max:5000';
            $base['bank_document'] = 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx';
        }

        $validated = $request->validate(CreateFormDuration::mergeRules($base));

        $okBranch = TblLocationModel::query()
            ->where('id', (int) $validated['branch_id'])
            ->where('zone_id', (int) $validated['zone_id'])
            ->exists();
        if (! $okBranch) {
            throw ValidationException::withMessages([
                'branch_id' => 'The selected branch must belong to the selected zone.',
            ]);
        }

        $linkMode = PaymentRequest::requiresPoAttachment($type)
            ? (string) ($validated['po_link_mode'] ?? 'po')
            : 'po';

        if ($type === PaymentRequest::TYPE_ADVANCE) {
            $linkMode = 'po';
        }
        if ($type === PaymentRequest::TYPE_SETTLEMENT) {
            $linkMode = 'bill';
        }

        $linkedBill = null;
        if (PaymentRequest::requiresPoAttachment($type) && $linkMode === 'po' && ! empty($validated['bill_id'])) {
            $msg = $type === PaymentRequest::TYPE_ADVANCE
                ? 'Advance is linked by PO only. Remove the bill reference.'
                : 'You chose “Against purchase order (PO)”. Clear the bill or switch to “Against vendor bill”.';
            throw ValidationException::withMessages([
                'po_link_mode' => $msg,
            ]);
        }
        if (PaymentRequest::requiresPoAttachment($type) && $linkMode === 'bill' && empty($validated['bill_id'])) {
            $billMsg = $type === PaymentRequest::TYPE_SETTLEMENT
                ? 'Settlement is against a vendor bill. Enter the bill number, tap Load, then submit.'
                : 'Choose “Against vendor bill”, enter the bill reference, tap Load, then submit.';
            throw ValidationException::withMessages([
                'bill_id' => $billMsg,
            ]);
        }
        $linkedBillFinancial = null;
        if (! empty($validated['bill_id'])) {
            $linkedBill = Tblbill::query()
                ->where('id', (int) $validated['bill_id'])
                ->where(function ($q) {
                    $q->where('delete_status', 0)->orWhereNull('delete_status');
                })
                ->first();
            if (! $linkedBill) {
                throw ValidationException::withMessages([
                    'bill_id' => 'Bill not found or is not active.',
                ]);
            }
            $billVendorTblId = $this->resolveVendorTableIdForBill($linkedBill);
            if ($billVendorTblId && (int) $validated['vendor_id'] !== $billVendorTblId) {
                throw ValidationException::withMessages([
                    'vendor_id' => 'Vendor must match the vendor on the selected bill.',
                ]);
            }
            $linkedBillFinancial = $this->billFinancialSnapshot($linkedBill);
            $reqAmt = (float) $validated['amount'];
            /** Exclude this request's own pending amount from headroom check (it's being replaced by the new amount). */
            $selfAmount = ((int) $paymentRequest->bill_id === (int) $linkedBill->id
                && in_array($paymentRequest->status, [PaymentRequest::STATUS_PENDING, PaymentRequest::STATUS_APPROVED], true))
                ? (float) $paymentRequest->amount
                : 0.0;
            $payableForUpdate = $linkedBillFinancial['payable'] + $selfAmount;
            if ($reqAmt - $payableForUpdate > 0.02) {
                throw ValidationException::withMessages([
                    'amount' => 'This amount (₹'.number_format($reqAmt, 2)
                        .') is more than the balance left on the vendor bill (₹'.number_format($payableForUpdate, 2)
                        .'). That limit includes Bill Made / bank payments and other pending payment requests on this bill.',
                ]);
            }
        }

        $po = null;
        if (PaymentRequest::requiresPoAttachment($type)) {
            if ($linkMode === 'bill') {
                if ($linkedBill && $linkedBill->purchase_id) {
                    $po = TblPurchaseorder::query()
                        ->where('delete_status', 0)
                        ->where('id', (int) $linkedBill->purchase_id)
                        ->first();
                    if (! $po) {
                        throw ValidationException::withMessages([
                            'bill_id' => 'The bill is linked to a purchase order that could not be found.',
                        ]);
                    }
                }
            } else {
                $g = trim((string) ($validated['purchase_gen_order'] ?? ''));
                if ($g === '') {
                    throw ValidationException::withMessages([
                        'purchase_gen_order' => 'Enter the PO number (purchase_gen_order) or switch to Vendor bill.',
                    ]);
                }
                $po = TblPurchaseorder::query()->where('purchase_gen_order', $g)->first();
                if (! $po) {
                    throw ValidationException::withMessages([
                        'purchase_gen_order' => 'No purchase order found for this PO number (purchase_gen_order).',
                    ]);
                }
            }

            if ($po) {
                $poTotal = (float) ($po->grand_total_amount ?? 0);
                if ($poTotal <= 0) {
                    $errKey = $linkMode === 'bill' ? 'bill_id' : 'purchase_gen_order';
                    throw ValidationException::withMessages([
                        $errKey => 'Selected PO has no valid grand total on file.',
                    ]);
                }
                $remainingPo = $this->remainingOnPurchaseOrderAgainstMergedHistory($po, (int) $paymentRequest->id);
                $thisAmount = (float) $validated['amount'];
                if ($thisAmount - $remainingPo > 0.02) {
                    throw ValidationException::withMessages([
                        'amount' => 'This amount (₹'.number_format($thisAmount, 2)
                            .') is more than the remaining headroom on this purchase order (₹'.number_format($remainingPo, 2)
                            .'). That limit includes vendor bill / NEFT payments and other payment requests already against this PO.',
                    ]);
                }
            }
        }

        $poPath = $paymentRequest->po_attachment_path;
        $docPath = $paymentRequest->document_attachment_path;
        $bankDocPath = $paymentRequest->bank_document_path;

        if (PaymentRequest::requiresPoAttachment($type)) {
            if ($request->hasFile('po_attachment')) {
                $newPoPath = $this->saveUploaded($request->file('po_attachment'), 'superadmin/payment_requests');
                $this->deleteAttachmentFile($paymentRequest->po_attachment_path);
                $poPath = $newPoPath;
            }
            if ($paymentRequest->document_attachment_path) {
                $this->deleteAttachmentFile($paymentRequest->document_attachment_path);
                $docPath = null;
            }
        } else {
            if ($request->hasFile('document_attachment')) {
                $newDocPath = $this->saveUploaded($request->file('document_attachment'), 'superadmin/payment_requests');
                $this->deleteAttachmentFile($paymentRequest->document_attachment_path);
                $docPath = $newDocPath;
            }
            if ($paymentRequest->po_attachment_path) {
                $this->deleteAttachmentFile($paymentRequest->po_attachment_path);
                $poPath = null;
            }
        }

        if (PaymentRequest::requiresPayeeBankDetails($type)) {
            if ($request->hasFile('bank_document')) {
                $newBankPath = $this->saveUploaded($request->file('bank_document'), 'superadmin/payment_requests');
                $this->deleteAttachmentFile($paymentRequest->bank_document_path);
                $bankDocPath = $newBankPath;
            }
        } else {
            if ($paymentRequest->bank_document_path) {
                $this->deleteAttachmentFile($paymentRequest->bank_document_path);
                $bankDocPath = null;
            }
        }

        if (PaymentRequest::requiresPoAttachment($type) && empty($poPath)) {
            throw ValidationException::withMessages([
                'po_attachment' => 'Please attach the PO / vendor bill document.',
            ]);
        }
        if (! PaymentRequest::requiresPoAttachment($type) && empty($docPath)) {
            throw ValidationException::withMessages([
                'document_attachment' => 'Please attach the supporting document.',
            ]);
        }
        if (PaymentRequest::requiresPayeeBankDetails($type) && empty($bankDocPath)) {
            throw ValidationException::withMessages([
                'bank_document' => 'Please attach the bank document (cheque / statement / passbook).',
            ]);
        }

        $finalVendorId = $validated['vendor_id'] ?? null;
        if (empty($finalVendorId) && $po && $po->vendor_id) {
            $finalVendorId = $po->vendor_id;
        }

        $paymentRequest->fill([
            'company_id' => $validated['company_id'] ?? null,
            'zone_id' => (int) $validated['zone_id'],
            'branch_id' => (int) $validated['branch_id'],
            'vendor_id' => $finalVendorId,
            'payment_type' => $type,
            'amount' => $validated['amount'],
            'purchase_order_id' => $po?->id,
            'bill_id' => PaymentRequest::requiresPoAttachment($type)
                ? (($linkMode === 'bill' && $linkedBill) ? $linkedBill->id : null)
                : ($linkedBill?->id),
            'bill_total_snapshot' => $linkedBill ? (float) ($linkedBill->grand_total_amount ?? 0) : null,
            'bill_balance_snapshot' => $linkedBillFinancial ? $linkedBillFinancial['reconciled_remaining'] : null,
            'po_total_snapshot' => $po ? (float) ($po->grand_total_amount ?? 0) : null,
            'po_attachment_path' => $poPath,
            'document_attachment_path' => $docPath,
            'bank_account_number' => $validated['bank_account_number'] ?? null,
            'bank_ifsc_code' => isset($validated['bank_ifsc_code']) ? strtoupper($validated['bank_ifsc_code']) : null,
            'bank_branch_details' => $validated['bank_branch_details'] ?? null,
            'bank_document_path' => $bankDocPath,
            'remarks' => $validated['remarks'] ?? null,
        ]);
        $paymentRequest->save();

        $successMessage = 'Payment request updated successfully.';
        if ($request->expectsJson()) {
            session()->flash('success', $successMessage);

            return response()->json([
                'ok' => true,
                'redirect' => route('superadmin.payment-requests.show', $paymentRequest),
            ]);
        }

        return redirect()
            ->route('superadmin.payment-requests.show', $paymentRequest)
            ->with('success', $successMessage);
    }

    /**
     * Best-effort cleanup of an old attachment under public/payment_request_attachments
     * (legacy uploads/ paths predate this folder and are left in place).
     */
    private function deleteAttachmentFile(?string $storedPath): void
    {
        if ($storedPath === null || $storedPath === '') {
            return;
        }
        if (str_starts_with($storedPath, 'uploads/')) {
            return;
        }
        $name = basename(str_replace('\\', '/', $storedPath));
        if ($name === '' || $name === '.' || $name === '..') {
            return;
        }
        $abs = public_path('payment_request_attachments/'.$name);
        if (File::exists($abs)) {
            try {
                File::delete($abs);
            } catch (\Throwable $e) {
                // ignore: file system cleanup is best-effort
            }
        }
    }

    public function approve(PaymentRequest $paymentRequest): RedirectResponse
    {
        $u = $this->userRow();
        if ((int) ($u->access_limits ?? 0) !== 1) {
            abort(403, 'You are not authorized to approve payment requests.');
        }
        if (! $paymentRequest->isPendingReview()) {
            return back()->with('error', 'This payment request is no longer pending review.');
        }

        DB::transaction(function () use ($paymentRequest): void {
            $paymentRequest->status = PaymentRequest::STATUS_APPROVED;
            $paymentRequest->reviewed_by = (int) auth()->id();
            $paymentRequest->reviewed_at = now();
            $paymentRequest->rejection_reason = null;
            $paymentRequest->save();

            $this->applyApprovedPoLinkedLedger($paymentRequest);
        });

        return back()->with('success', 'Payment request approved.');
    }

    public function reject(Request $request, PaymentRequest $paymentRequest): RedirectResponse
    {
        $u = $this->userRow();
        if ((int) ($u->access_limits ?? 0) !== 1) {
            abort(403, 'You are not authorized to reject payment requests.');
        }
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:5000',
        ]);
        if (! $paymentRequest->isPendingReview()) {
            return back()->with('error', 'This payment request is no longer pending review.');
        }
        $paymentRequest->status = PaymentRequest::STATUS_REJECTED;
        $paymentRequest->reviewed_by = (int) auth()->id();
        $paymentRequest->reviewed_at = now();
        $paymentRequest->rejection_reason = $validated['rejection_reason'];
        $paymentRequest->save();

        return back()->with('success', 'Payment request rejected.');
    }

    public function show(PaymentRequest $paymentRequest): View
    {
        $u = $this->userRow();
        if (! $this->isPaymentRequestSuperAdmin($u)) {
            if ((int) $paymentRequest->created_by !== (int) auth()->id()) {
                abort(403, 'You can only view payment requests you created.');
            }
        }
        $paymentRequest->load([
            'branch:id,name,zone_id',
            'company:id,company_name',
            'zone:id,name',
            'legacyPurchaseOrder',
            'sourceBill:id,bill_gen_number,bill_number,vendor_id,delete_status,grand_total_amount,balance_amount,partially_payment,purchase_id',
            'sourceVendor:id,display_name,company_name,vendor_id',
            'creator:id,user_fullname',
            'reviewer:id,user_fullname',
            'linkedBills:id,payment_request_id,grand_total_amount,balance_amount,bill_gen_number,bill_number,vendor_id,delete_status,purchase_id',
        ]);
        $paidBefore = 0.0;
        if ($paymentRequest->purchase_order_id) {
            $paidBefore = (float) PaymentRequest::query()
                ->where('purchase_order_id', $paymentRequest->purchase_order_id)
                ->where('id', '<', $paymentRequest->id)
                ->countingTowardPo()
                ->sum('amount');
        }
        $poTotal = (float) ($paymentRequest->po_total_snapshot ?? 0);
        $thisAmount = (float) $paymentRequest->amount;
        $paidIncludingThis = $paidBefore + $thisAmount;
        $poPreviouslyPaidTotal = null;
        $poPaidOutsideRequests = null;
        $poBillPayLineRows = [];
        $poPrRequestRows = [];
        $poPrRequestsTotal = 0.0;
        if ($paymentRequest->purchase_order_id) {
            $poId = (int) $paymentRequest->purchase_order_id;
            $poMerged = $this->purchaseOrderPastPaymentHistoryMerged($poId);
            $poPreviouslyPaidTotal = round(array_sum(array_column($poMerged, 'amount')), 2);
            $poBillPayLineRows = $this->billPayLineRowsForPurchaseOrderDisplay($poId);
            $poBillPayTotal = round(array_sum(array_column($poBillPayLineRows, 'amount')), 2);
            if ($poBillPayTotal > 0.005) {
                $poPaidOutsideRequests = $poBillPayTotal;
            }
            $poPrRequestRows = $this->paymentRequestHistoryForPurchaseOrderAll($poId);
            $poPrRequestsTotal = round(array_sum(array_column($poPrRequestRows, 'amount')), 2);
        }
        /** Headroom from merged bill lines + de-duplicated PR activity (matches create / NEFT-style PO view). */
        $remainingAfter = $paymentRequest->purchase_order_id
            ? max(0.0, $poTotal - (float) $poPreviouslyPaidTotal)
            : max(0.0, $poTotal - $paidIncludingThis);

        $billPaidBefore = 0.0;
        $billTotalSnap = null;
        $billPaidIncluding = null;
        $billRemainingAfter = null;
        $billPaidOutsideRequests = null;
        $billPreviouslyPaidTotal = null;
        $showBillSettlement = false;
        $billPastPayments = [];
        $billPrRequestRows = [];
        $billPrRequestsTotal = 0.0;
        $billSettlementSource = null;
        $billSettlementSourceIsLinked = false;

        if ($paymentRequest->bill_id && $paymentRequest->sourceBill && (int) ($paymentRequest->sourceBill->delete_status ?? 0) === 0) {
            $billSettlementSource = $paymentRequest->sourceBill;
        } else {
            /** Bill was created later with bill_pr_link_mode = 'payment_request'; surface the first valid linked bill. */
            $linked = $paymentRequest->linkedBills->first(static function ($b) {
                return (int) ($b->delete_status ?? 0) === 0;
            });
            if ($linked) {
                $billSettlementSource = $linked;
                $billSettlementSourceIsLinked = true;
            }
        }

        if ($billSettlementSource) {
            $showBillSettlement = true;
            $billId = (int) $billSettlementSource->id;
            $billPaidBefore = (float) PaymentRequest::query()
                ->where('bill_id', $billId)
                ->where('id', '<', $paymentRequest->id)
                ->countingTowardBill()
                ->sum('amount');
            $billTotalSnap = (float) (
                $paymentRequest->bill_total_snapshot
                ?? $billSettlementSource->grand_total_amount
                ?? 0
            );
            $billPaidIncluding = $billPaidBefore + $thisAmount;
            $liveBill = Tblbill::query()
                ->whereKey($billId)
                ->where(function ($q) {
                    $q->where('delete_status', 0)->orWhereNull('delete_status');
                })
                ->first(['id', 'grand_total_amount', 'balance_amount', 'partially_payment']);
            if ($liveBill) {
                $bf = $this->billFinancialSnapshot($liveBill);
                $billPaidOutsideRequests = $bf['paid_outside_requests'];
                $billRemainingAfter = max(0.0, $bf['reconciled_remaining'] - $bf['pending_pr']);
                $billPreviouslyPaidTotal = max(0.0, $bf['grand'] - $bf['reconciled_remaining']);
            } else {
                $billRemainingAfter = max(0.0, $billTotalSnap - $billPaidIncluding);
            }
            $billPastPayments = $this->billPayLinesHistoryForBill($billId);
            $billPrRequestRows = $this->paymentRequestHistoryForBill($billId);
            $billPrRequestsTotal = round(array_sum(array_column($billPrRequestRows, 'amount')), 2);
        }

        return view('superadmin.payment_requests.show', [
            'admin' => $u,
            'r' => $paymentRequest,
            'po_paid_before' => $paidBefore,
            'po_paid_including' => $paidIncludingThis,
            'po_remaining_after' => $remainingAfter,
            'po_previously_paid_total' => $poPreviouslyPaidTotal,
            'po_paid_outside_requests' => $poPaidOutsideRequests,
            'po_bill_pay_line_rows' => $poBillPayLineRows,
            'po_pr_request_rows' => $poPrRequestRows,
            'po_pr_requests_total' => $poPrRequestsTotal,
            'show_bill_settlement' => $showBillSettlement,
            'bill_paid_before' => $billPaidBefore,
            'bill_total_snap' => $billTotalSnap,
            'bill_paid_including' => $billPaidIncluding,
            'bill_remaining_after' => $billRemainingAfter,
            'bill_paid_outside_requests' => $billPaidOutsideRequests,
            'bill_previously_paid_total' => $billPreviouslyPaidTotal,
            'bill_past_payments' => $billPastPayments,
            'bill_pr_request_rows' => $billPrRequestRows,
            'bill_pr_requests_total' => $billPrRequestsTotal,
            'bill_settlement_source' => $billSettlementSource,
            'bill_settlement_via_linked_bill' => $billSettlementSourceIsLinked,
            'bill_panel_redundant_with_po' => (
                $billSettlementSource
                && $paymentRequest->purchase_order_id
                && (int) ($billSettlementSource->purchase_id ?? 0) === (int) $paymentRequest->purchase_order_id
            ),
        ]);
    }

    /**
     * When a PO-linked request is approved: record vendor “payment made” + line against the linked bill,
     * or reduce PO balance when the request is anchored on the PO only (no bill). Settlement without a bill is skipped.
     */
    private function applyApprovedPoLinkedLedger(PaymentRequest $pr): void
    {
        if (! in_array($pr->payment_type, PaymentRequest::PO_LINKED_TYPES, true)) {
            return;
        }

        $amount = (float) $pr->amount;
        if ($amount < 0.0001) {
            return;
        }

        if ($pr->bill_id) {
            $this->createBillPartPaymentFromApprovedPaymentRequest($pr, $amount);

            return;
        }

        if (! $pr->purchase_order_id) {
            return;
        }

        if ($pr->payment_type === PaymentRequest::TYPE_SETTLEMENT) {
            return;
        }

        $this->decrementPurchaseOrderBalanceFromApprovedPaymentRequest($pr, $amount);
    }

    private function nextBillPaymentGenOrder(): string
    {
        $lastRecord = Tblbillpay::query()->orderByDesc('id')->first();
        if ($lastRecord && ! empty($lastRecord->payment_gen_order)) {
            $lastNumber = (int) str_replace('PAYMENT-', '', (string) $lastRecord->payment_gen_order);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return 'PAYMENT-'.str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);
    }

    private function createBillPartPaymentFromApprovedPaymentRequest(PaymentRequest $pr, float $amount): void
    {
        $bill = Tblbill::query()
            ->whereKey((int) $pr->bill_id)
            ->where(function ($q) {
                $q->where('delete_status', 0)->orWhereNull('delete_status');
            })
            ->lockForUpdate()
            ->first();

        if (! $bill) {
            return;
        }

        $balanceBefore = (float) ($bill->balance_amount ?? 0);
        $applyAmount = min($amount, max(0.0, $balanceBefore));
        if ($applyAmount < 0.0001) {
            return;
        }

        $eps = 0.02;
        $now = now();
        $userId = (int) auth()->id();
        $admin = auth()->user();
        $adminEmail = is_object($admin) && isset($admin->email) ? (string) $admin->email : '';

        $docNames = [];
        if ($pr->po_attachment_path) {
            $base = basename(str_replace('\\', '/', (string) $pr->po_attachment_path));
            if ($base !== '' && $base !== '.' && $base !== '..') {
                $docNames[] = $base;
            }
        }

        $billPay = Tblbillpay::create([
            'user_id' => $userId,
            'vendor_id' => (int) $bill->vendor_id,
            'vendor_name' => (string) ($bill->vendor_name ?? ''),
            'zone_id' => $bill->zone_id,
            'zone_name' => (string) ($bill->zone_name ?? ''),
            'branch_id' => $bill->branch_id,
            'branch_name' => (string) ($bill->branch_name ?? ''),
            'company_id' => $bill->company_id,
            'company_name' => (string) ($bill->company_name ?? ''),
            'payment' => (string) $applyAmount,
            'payment_gen_order' => $this->nextBillPaymentGenOrder(),
            'payment_made' => 'Payment request '.(string) $pr->request_no,
            'payment_date' => $now->format('d/m/Y'),
            'payment_mode' => 'Payment Request',
            'paid_through' => 'Superadmin approval',
            'reference' => (string) $pr->request_no,
            'remark' => Str::limit((string) ($pr->remarks ?? ''), 500),
            'save_status' => 1,
            'amount_paid' => $applyAmount,
            'amount_used' => $applyAmount,
            'amount_refunded' => 0,
            'amount_excess' => 0,
            'note' => '',
            'documents' => json_encode($docNames),
        ]);

        $newBalance = max(0.0, $balanceBefore - $applyAmount);
        $newPartial = (float) ($bill->partially_payment ?? 0) + $applyAmount;
        $isPaid = $newBalance <= $eps;

        if ($isPaid) {
            Tblbill::where('id', $bill->id)->update([
                'partially_payment' => $newPartial,
                'balance_amount' => 0,
                'bill_made_status' => 1,
                'bill_status' => 'Paid',
            ]);
        } else {
            Tblbill::where('id', $bill->id)->update([
                'partially_payment' => $newPartial,
                'balance_amount' => $newBalance,
                'bill_status' => 'Partially Payed',
            ]);
        }

        TblBillPayLines::create([
            'bill_pay_id' => $billPay->id,
            'bill_id' => $bill->id,
            'bill_date' => $bill->bill_date,
            'due_date' => $bill->due_date,
            'bill_number' => $bill->bill_number,
            'grand_total_amount' => $bill->grand_total_amount,
            'balance_amount' => $balanceBefore,
            'payment_date' => $now->format('d/m/Y'),
            'amount' => $applyAmount,
            'created_at' => $now,
        ]);

        $vendorRow = Tblvendor::query()->where('id', $bill->vendor_id)->first();
        if ($vendorRow) {
            TblVendorHistory::create([
                'vendor_id' => $vendorRow->id,
                'name' => 'Payments Made added',
                'description' => 'Payment of amount ₹'.number_format($applyAmount, 2)
                    .' from payment request '.(string) $pr->request_no
                    .($adminEmail !== '' ? ' by '.$adminEmail : ''),
                'date' => $now->toDateString(),
                'time' => $now->format('h:i A'),
            ]);
        }
    }

    private function decrementPurchaseOrderBalanceFromApprovedPaymentRequest(PaymentRequest $pr, float $amount): void
    {
        $po = TblPurchaseorder::query()
            ->whereKey((int) $pr->purchase_order_id)
            ->where(function ($q) {
                $q->where('delete_status', 0)->orWhereNull('delete_status');
            })
            ->lockForUpdate()
            ->first();

        if (! $po) {
            return;
        }

        $current = (float) ($po->balance_amount ?? $po->grand_total_amount ?? 0);
        if ($current < 0.0001) {
            return;
        }

        $applyAmount = min($amount, $current);
        $newBal = max(0.0, $current - $applyAmount);

        TblPurchaseorder::where('id', $po->id)->update([
            'balance_amount' => $newBal,
        ]);
    }
}
