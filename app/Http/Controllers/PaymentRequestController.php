<?php

namespace App\Http\Controllers;

use App\Models\PaymentRequest;
use App\Models\TblLocationModel;
use App\Models\TblPurchaseorder;
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
        $dir = public_path('uploads/'.$subDir);
        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }
        $ext = strtolower($file->getClientOriginalExtension() ?: 'bin');
        $fn = time().'_'.Str::random(8).'.'.$ext;
        $file->move($dir, $fn);

        return 'uploads/'.$subDir.'/'.$fn;
    }

    public function index(Request $request): View
    {
        $u = $this->userRow();
        $loc = $this->locationDropdownData();
        $vendors = Tblvendor::query()
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

        $rows = (clone $query)->orderByDesc('id')->paginate(25)->withQueryString();

        return view('superadmin.payment_requests.index', [
            'admin' => $u,
            'rows' => $rows,
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
        $paidBefore = (float) PaymentRequest::query()
            ->where('purchase_order_id', $po->id)
            ->countingTowardPo()
            ->sum('amount');
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

        return response()->json([
            'ok' => true,
            'purchase_order_id' => (int) $po->id,
            'purchase_gen_order' => (string) ($po->purchase_gen_order ?? ''),
            'order_number' => (string) ($po->order_number ?? $po->purchase_order_number ?? $po->purchase_gen_order ?? ''),
            'po_total' => $poTotal,
            'amount_paid_before' => $paidBefore,
            'remaining_before_new' => max(0, $poTotal - $paidBefore),
            'vendor_name' => $vendorName,
            'vendor_id' => $po->vendor_id,
            'po_pdf_url' => route('superadmin.getpurchasepdf', ['id' => $po->id]),
        ]);
    }

    public function store(Request $request): RedirectResponse
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
        ];

        if (PaymentRequest::requiresPoAttachment($type)) {
            $base['purchase_gen_order'] = 'required|string|max:100';
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

        $po = null;
        if (PaymentRequest::requiresPoAttachment($type)) {
            $g = trim((string) $validated['purchase_gen_order']);
            $po = TblPurchaseorder::query()->where('purchase_gen_order', $g)->first();
            if (! $po) {
                throw ValidationException::withMessages([
                    'purchase_gen_order' => 'No purchase order found for this PO number (purchase_gen_order).',
                ]);
            }
            $poTotal = (float) ($po->grand_total_amount ?? 0);
            if ($poTotal <= 0) {
                throw ValidationException::withMessages([
                    'purchase_gen_order' => 'Selected PO has no valid grand total on file.',
                ]);
            }
            $paidBefore = (float) PaymentRequest::query()
                ->where('purchase_order_id', $po->id)
                ->countingTowardPo()
                ->sum('amount');
            $thisAmount = (float) $validated['amount'];
            if ($thisAmount + $paidBefore - $poTotal > 0.01) {
                throw ValidationException::withMessages([
                    'amount' => 'This amount plus prior payment requests ('
                        .number_format($paidBefore, 2)
                        .') would exceed the PO total ('.number_format($poTotal, 2).').',
                ]);
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

        return redirect()
            ->route('superadmin.payment-requests.show', $created)
            ->with('success', 'Payment request submitted.');
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
        $paymentRequest->status = PaymentRequest::STATUS_APPROVED;
        $paymentRequest->reviewed_by = (int) auth()->id();
        $paymentRequest->reviewed_at = now();
        $paymentRequest->rejection_reason = null;
        $paymentRequest->save();

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
            'sourceVendor:id,display_name,company_name,vendor_id',
            'creator:id,user_fullname',
            'reviewer:id,user_fullname',
            'linkedBills:id,payment_request_id,grand_total_amount,balance_amount,bill_gen_number,bill_number,vendor_id,delete_status',
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
        $remainingAfter = max(0, $poTotal - $paidIncludingThis);

        return view('superadmin.payment_requests.show', [
            'admin' => $u,
            'r' => $paymentRequest,
            'po_paid_before' => $paidBefore,
            'po_paid_including' => $paidIncludingThis,
            'po_remaining_after' => $remainingAfter,
        ]);
    }
}
