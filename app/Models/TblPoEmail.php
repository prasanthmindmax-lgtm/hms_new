<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TblPoEmail extends Model
{
    use HasFactory;

    protected $table = 'po_email_tbl';

    protected $fillable = [
        'email',
        'label',
        'to_email',
        'cc_emails',
        'menu_type',
        'mobile_number',
        'status',
        'user_id',
        'created_by',
    ];

    protected $casts = [
        'cc_emails' => 'array',
        'status'    => 'integer',
    ];

    /* ── Scopes ── */
    public function scopeActive($q)       { return $q->where('status', 1); }
    public function scopeForMenu($q, $m)  { return $q->where('menu_type', $m); }

    /* ── Helpers ── */
    public function getMainEmail(): string
    {
        return $this->to_email ?: $this->email ?: '';
    }

    public function getCcArray(): array
    {
        if (is_array($this->cc_emails)) return $this->cc_emails;
        if (is_string($this->cc_emails)) {
            $decoded = json_decode($this->cc_emails, true);
            return is_array($decoded) ? $decoded : [];
        }
        return [];
    }
}
