<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VmsVisitor extends Model
{
    use HasFactory;

    protected $table = 'vms_visitors';

    protected $fillable = [
        'qr_code_id', 'visitor_name', 'visitor_phone', 'visitor_email',
        'visitor_type', 'company_name', 'purpose', 'person_to_meet',
        'department', 'appointment_time', 'equipment_carried',
        'id_type', 'id_number', 'photo', 'declaration_agreed',
        'badge_number', 'branch', 'branch_type', 'location_id', 'status',
        'entry_time', 'exit_time',
        'approved_by', 'approved_at', 'rejection_reason',
    ];

    protected $casts = [
        'declaration_agreed' => 'boolean',
        'entry_time'         => 'datetime',
        'exit_time'          => 'datetime',
        'approved_at'        => 'datetime',
    ];

    public function qrCode()
    {
        return $this->belongsTo(VmsQrCode::class, 'qr_code_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(\App\Models\usermanagementdetails::class, 'approved_by');
    }

    public function getDurationAttribute(): string
    {
        if (!$this->entry_time) return '—';
        $end = $this->exit_time ?? now();
        $mins = $this->entry_time->diffInMinutes($end);
        if ($mins < 60) return $mins . ' min';
        return floor($mins / 60) . 'h ' . ($mins % 60) . 'm';
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'approved', 'inside' => 'green',
            'pending'            => 'orange',
            'rejected'           => 'red',
            'checked_out'        => 'muted',
            default              => 'muted',
        };
    }

    public function getVisitorTypeLabelAttribute(): string
    {
        return match ($this->visitor_type) {
            'pharma'          => 'Pharma Vendor',
            'non_pharma'      => 'Non-Pharma Vendor',
            'patient_relative'=> 'Patient Relative',
            'job_applicant'   => 'Job Applicant',
            'government'      => 'Government Official',
            default           => ucfirst(str_replace('_', ' ', $this->visitor_type)),
        };
    }
}
