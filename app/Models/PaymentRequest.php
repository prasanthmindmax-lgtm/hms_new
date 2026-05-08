<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentRequest extends Model
{
    protected $table = 'payment_requests';

    public const TYPE_ADVANCE = 'advance';

    public const TYPE_PART_PAYMENT = 'part_payment';

    public const TYPE_SETTLEMENT = 'settlement';

    public const TYPE_PETTY_CASH_ADVANCE = 'petty_cash_advance';

    public const TYPE_REIMBURSEMENT = 'reimbursement';

    public const TYPE_REFUND = 'refund';

    public const TYPE_PATIENT_REFUND = 'patient_refund';

    public const TYPE_INSTANT_PAYMENT = 'instant_payment';

    public const TYPE_MISCELLANEOUS = 'miscellaneous';

    public const TYPES = [
        self::TYPE_ADVANCE,
        self::TYPE_PART_PAYMENT,
        self::TYPE_SETTLEMENT,
        // self::TYPE_PETTY_CASH_ADVANCE,
        // self::TYPE_REIMBURSEMENT,
        self::TYPE_REFUND,
        self::TYPE_PATIENT_REFUND,
        self::TYPE_INSTANT_PAYMENT,
        self::TYPE_MISCELLANEOUS,
    ];

    /** @var array<string, string> */
    public const TYPE_LABELS = [
        self::TYPE_ADVANCE => 'Advance',
        self::TYPE_PART_PAYMENT => 'Part Payment',
        self::TYPE_SETTLEMENT => 'Settlement',
        // self::TYPE_PETTY_CASH_ADVANCE => 'Petty Cash Advance',
        // self::TYPE_REIMBURSEMENT => 'Reimbursement',
        self::TYPE_REFUND => 'Refund',
        self::TYPE_PATIENT_REFUND => 'Patient Refund Payment',
        self::TYPE_INSTANT_PAYMENT => 'Instant Payment',
        self::TYPE_MISCELLANEOUS => 'Miscellaneous Payment',
    ];

    public const PAYOUT_ONLY_TYPES = [
        self::TYPE_PETTY_CASH_ADVANCE,
        self::TYPE_REIMBURSEMENT,
        self::TYPE_REFUND,
        self::TYPE_PATIENT_REFUND,
        self::TYPE_INSTANT_PAYMENT,
        self::TYPE_MISCELLANEOUS,
    ];

    public const PO_LINKED_TYPES = [
        self::TYPE_ADVANCE,
        self::TYPE_PART_PAYMENT,
        self::TYPE_SETTLEMENT,
    ];

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_APPROVED,
        self::STATUS_REJECTED,
    ];

    /** @var array<string, string> */
    public const STATUS_LABELS = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_APPROVED => 'Approved',
        self::STATUS_REJECTED => 'Rejected',
    ];

    /** No vendor bill linked to this payment request yet. */
    public const BILL_DISBURSE_NONE = 'none';

    /** Bill exists; no vendor payment recorded against it yet. */
    public const BILL_DISBURSE_UNPAID = 'unpaid';

    /** At least one payment applied; bill balance remains. */
    public const BILL_DISBURSE_PARTIAL = 'partial';

    /** Linked bill(s) fully settled (balance at or near zero). */
    public const BILL_DISBURSE_PAID = 'paid';

    public const BILL_DISBURSE_STATES = [
        self::BILL_DISBURSE_NONE,
        self::BILL_DISBURSE_UNPAID,
        self::BILL_DISBURSE_PARTIAL,
        self::BILL_DISBURSE_PAID,
    ];

    /** @var array<string, string> */
    public const BILL_DISBURSE_LABELS = [
        self::BILL_DISBURSE_NONE => 'No bill yet',
        self::BILL_DISBURSE_UNPAID => 'Unpaid',
        self::BILL_DISBURSE_PARTIAL => 'Partially paid',
        self::BILL_DISBURSE_PAID => 'Fully paid',
    ];

    private const BILL_DISBURSE_EPS = 0.02;

    protected $fillable = [
        'request_no',
        'company_id',
        'zone_id',
        'branch_id',
        'vendor_id',
        'payment_type',
        'amount',
        'purchase_order_id',
        'bill_id',
        'bill_total_snapshot',
        'bill_balance_snapshot',
        'po_total_snapshot',
        'po_attachment_path',
        'document_attachment_path',
        'bank_account_number',
        'bank_ifsc_code',
        'bank_branch_details',
        'bank_document_path',
        'remarks',
        'status',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'po_total_snapshot' => 'decimal:2',
        'bill_total_snapshot' => 'decimal:2',
        'bill_balance_snapshot' => 'decimal:2',
        'reviewed_at' => 'datetime',
    ];

    public static function typeLabel(string $type): string
    {
        return self::TYPE_LABELS[$type] ?? $type;
    }

    /**
     * Bootstrap 5.2+ badge (see components/badge). Two PO-linked types share a colour; labels differ.
     */
    public static function typePillClass(string $type): string
    {
        if (! in_array($type, self::TYPES, true)) {
            return 'badge rounded-pill text-bg-secondary text-wrap text-start';
        }

        $textBg = match ($type) {
            self::TYPE_ADVANCE => 'text-bg-primary',
            self::TYPE_PART_PAYMENT => 'text-bg-warning',
            self::TYPE_SETTLEMENT => 'text-bg-success',
            self::TYPE_PETTY_CASH_ADVANCE => 'text-bg-info',
            self::TYPE_REIMBURSEMENT => 'text-bg-secondary',
            self::TYPE_REFUND => 'text-bg-danger',
            self::TYPE_PATIENT_REFUND => 'text-bg-dark',
            self::TYPE_INSTANT_PAYMENT => 'text-bg-success',
            self::TYPE_MISCELLANEOUS => 'text-bg-light',
            default => 'text-bg-secondary',
        };

        $lightText = $type === self::TYPE_MISCELLANEOUS ? ' text-dark' : '';

        return 'badge rounded-pill text-wrap text-start'.$lightText.' '.$textBg;
    }

    public static function requiresPoAttachment(string $type): bool
    {
        return in_array($type, self::PO_LINKED_TYPES, true);
    }

    /** Petty cash, reimbursement, refunds, instant & misc.: payee bank details required on the form. */
    public static function requiresPayeeBankDetails(string $type): bool
    {
        return in_array($type, self::PAYOUT_ONLY_TYPES, true);
    }

    public static function statusLabel(string $status): string
    {
        return self::STATUS_LABELS[$status] ?? ucfirst(str_replace('_', ' ', $status));
    }

    public static function statusPillClass(string $status): string
    {
        return match ($status) {
            self::STATUS_APPROVED => 'badge rounded-pill text-bg-success',
            self::STATUS_REJECTED => 'badge rounded-pill text-bg-danger',
            self::STATUS_PENDING => 'badge rounded-pill text-bg-warning text-dark',
            default => 'badge rounded-pill text-bg-secondary',
        };
    }

    public static function billDisbursementLabel(string $state): string
    {
        return self::BILL_DISBURSE_LABELS[$state] ?? ucfirst(str_replace('_', ' ', $state));
    }

    public static function billDisbursementPillClass(string $state): string
    {
        return match ($state) {
            self::BILL_DISBURSE_PAID => 'badge rounded-pill text-bg-success',
            self::BILL_DISBURSE_PARTIAL => 'badge rounded-pill text-bg-info text-dark',
            self::BILL_DISBURSE_UNPAID => 'badge rounded-pill text-bg-secondary',
            self::BILL_DISBURSE_NONE => 'badge rounded-pill text-bg-light text-dark',
            default => 'badge rounded-pill text-bg-secondary',
        };
    }
    public function billDisbursementState(): string
    {
        $bills = collect();

        if ($this->bill_id) {
            $src = $this->relationLoaded('sourceBill') ? $this->sourceBill : $this->sourceBill()->first([
                'id', 'grand_total_amount', 'balance_amount', 'delete_status',
            ]);
            if ($src && (int) ($src->delete_status ?? 0) === 0) {
                $bills->push($src);
            }
        }

        if ($bills->isEmpty()) {
            $linked = $this->relationLoaded('linkedBills')
                ? $this->linkedBills
                : $this->linkedBills()->get(['id', 'grand_total_amount', 'balance_amount', 'delete_status']);
            foreach ($linked as $b) {
                if ((int) ($b->delete_status ?? 0) === 0) {
                    $bills->push($b);
                }
            }
        }

        if ($bills->isEmpty()) {
            return self::BILL_DISBURSE_NONE;
        }

        $totalGrand = 0.0;
        $totalBalance = 0.0;
        foreach ($bills as $bill) {
            $g = (float) ($bill->grand_total_amount ?? 0);
            $b = (float) ($bill->balance_amount ?? 0);
            $totalGrand += $g;
            $totalBalance += max(0.0, $b);
        }

        $totalPaid = max(0.0, $totalGrand - $totalBalance);

        if ($totalGrand <= self::BILL_DISBURSE_EPS && $totalBalance <= self::BILL_DISBURSE_EPS) {
            return self::BILL_DISBURSE_PAID;
        }
        if ($totalPaid <= self::BILL_DISBURSE_EPS) {
            return self::BILL_DISBURSE_UNPAID;
        }
        if ($totalBalance <= self::BILL_DISBURSE_EPS) {
            return self::BILL_DISBURSE_PAID;
        }

        return self::BILL_DISBURSE_PARTIAL;
    }

    public function isPendingReview(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Requests that still affect PO available balance (submitted pending approval or approved).
     */
    public function scopeCountingTowardPo(Builder $query): Builder
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_APPROVED]);
    }

    public function scopeCountingTowardBill(Builder $query): Builder
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_APPROVED]);
    }

    public static function attachmentPublicUrl(?string $storedPath): ?string
    {
        if ($storedPath === null || $storedPath === '') {
            return null;
        }
        if (str_starts_with($storedPath, 'uploads/')) {
            return asset($storedPath);
        }
        $name = basename(str_replace('\\', '/', $storedPath));
        if ($name === '' || $name === '.' || $name === '..') {
            return null;
        }

        return rtrim(asset('/public/payment_request_attachments'), '/').'/'.rawurlencode($name);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(TblLocationModel::class, 'branch_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Tblcompany::class, 'company_id');
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(TblZonesModel::class, 'zone_id');
    }

    public function sourceVendor(): BelongsTo
    {
        return $this->belongsTo(Tblvendor::class, 'vendor_id');
    }

    public function legacyPurchaseOrder(): BelongsTo
    {
        return $this->belongsTo(TblPurchaseorder::class, 'purchase_order_id');
    }

    public function sourceBill(): BelongsTo
    {
        return $this->belongsTo(Tblbill::class, 'bill_id');
    }

    public function linkedBills(): HasMany
    {
        return $this->hasMany(Tblbill::class, 'payment_request_id')
            ->where('bill_pr_link_mode', 'payment_request')
            ->where(function (Builder $q) {
                $q->where('delete_status', 0)->orWhereNull('delete_status');
            });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(usermanagementdetails::class, 'created_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(usermanagementdetails::class, 'reviewed_by');
    }
    
    public function getVendorDisplayNameAttribute(): string
    {
        if ($this->relationLoaded('sourceVendor') && $this->sourceVendor) {
            $n = trim((string) ($this->sourceVendor->display_name ?: $this->sourceVendor->company_name ?: ''));
            if ($n !== '') {
                return $n;
            }
        }
        if ($this->vendor_id) {
            $v = Tblvendor::query()
                ->where(function ($q) {
                    $q->where('id', $this->vendor_id)
                        ->orWhere('vendor_id', $this->vendor_id);
                })
                ->first(['display_name', 'company_name']);
            if ($v) {
                $n = trim((string) ($v->display_name ?: $v->company_name ?: ''));
                if ($n !== '') {
                    return $n;
                }
            }
        }
        $po = $this->relationLoaded('legacyPurchaseOrder') ? $this->legacyPurchaseOrder : null;
        if ($po) {
            return trim((string) ($po->vendor_name ?? ''));
        }

        return '';
    }
}
