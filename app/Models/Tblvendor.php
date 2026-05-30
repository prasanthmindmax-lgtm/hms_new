<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Tblvendor extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const PARTY_VENDOR = 'Vendor';

    public const PARTY_EMPLOYEE = 'Employee';

    public const PARTY_LANDLORD = 'Landlord';

    public const PARTY_MAINTENANCE = 'Maintenance Vendor';


    /** @var list<string> */
    public const PARTY_TYPES = [
        self::PARTY_VENDOR,
        self::PARTY_EMPLOYEE,
        self::PARTY_LANDLORD,
        self::PARTY_MAINTENANCE,
    ];

    protected $table = 'vendor_tbl';

    protected $fillable = [
        'user_id',
        'active_status',
        'vendor_id',
        'vendor_salutation',
        'vendor_first_name',
        'vendor_last_name',
        'company_name',
        'display_name',
        'email',
        'mobile',
        'work_phone',
        'pan_number',
        'pan_upload',
        'gst_number',
        'vendor_type_id',
        'vendor_type_name',
        'party_type',
        'reference',
        'opening_balance',
        'payment_terms',
        'portal_language',
        'website',
        'department',
        'designation',
        'twitter',
        'skype',
        'facebook',
        'tds_tax_id',
        'tds_tax_name',
        'tds_amount',
        'remarks',
        'documents',
    ];

    public function billingAddress()
    {
        return $this->hasOne(TblBilling::class, 'vendor_id');
    }

    public function shippingAddress()
    {
        return $this->hasOne(TblShipping::class, 'vendor_id');
    }

    public function contacts()
    {
        return $this->hasMany(TblContact::class, 'vendor_id');
    }

    public function bankdetails()
    {
        return $this->hasMany(Tblbankdetails::class, 'vendor_id');
    }

    public function tdstax()
    {
        return $this->belongsTo(Tbltdstax::class, 'tds_tax_id');
    }

    public function history()
    {
        return $this->hasMany(TblVendorHistory::class, 'vendor_id');
    }

    public function creator()
    {
        return $this->belongsTo(usermanagementdetails::class, 'user_id');
    }

    public function getCreatedByNameAttribute(): ?string
    {
        $name = trim((string) ($this->creator?->user_fullname ?? $this->creator?->username ?? ''));

        return $name !== '' ? $name : null;
    }

    public static function normalizePartyType(?string $type): ?string
    {
        $type = trim((string) $type);
        if ($type === '') {
            return null;
        }

        foreach (self::PARTY_TYPES as $allowed) {
            if (strcasecmp($type, $allowed) === 0) {
                return $allowed;
            }
        }

        return null;
    }

    /**
     * Dropdown / register label with sensible fallbacks so every vendor row can be shown.
     */
    public function listDisplayLabel(): string
    {
        $label = trim((string) ($this->display_name ?? ''));
        if ($label !== '') {
            return $label;
        }

        $label = trim((string) ($this->company_name ?? ''));
        if ($label !== '') {
            return $label;
        }

        $person = trim(implode(' ', array_filter([
            trim((string) ($this->vendor_first_name ?? '')),
            trim((string) ($this->vendor_last_name ?? '')),
        ])));

        if ($person !== '') {
            return $person;
        }

        $code = trim((string) ($this->vendor_id ?? ''));
        if ($code !== '') {
            return $code;
        }

        return 'Vendor #'.(int) $this->id;
    }

    public function scopeActiveLandlords(Builder $query): Builder
    {
        return $query
            ->where('active_status', 0)
            ->where('party_type', self::PARTY_LANDLORD);
    }

    /** All active rows in vendor master (active_status = 0). */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active_status', 0);
    }

    /** Active vendors for security / housekeeping service agreements. */
    public function scopeActiveServiceVendors(Builder $query): Builder
    {
        return $query
            ->where('active_status', 0)
            ->where('party_type', self::PARTY_VENDOR);
    }

    /** Active maintenance vendors for maintenance master bills. */
    public function scopeActiveMaintenanceVendors(Builder $query): Builder
    {
        return $query
            ->where('active_status', 0)
            ->where('party_type', self::PARTY_MAINTENANCE);
    }

    public function scopePartyType(Builder $query, ?string $type): Builder
    {
        $normalized = self::normalizePartyType($type);
        if ($normalized === null) {
            return $query;
        }

        return $query->where('party_type', $normalized);
    }
}
