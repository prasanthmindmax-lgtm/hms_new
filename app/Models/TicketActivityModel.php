<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TicketModel;

class TicketActivityModel extends Model
{
    use HasFactory;

    protected $table = 'tbl_ticket_activities';
    protected $fillable = [
        'ticket_id',
        'staff_id',
        'priotity_level',
        'ticket_status',
        'description',
        'department_id',
        'sub_department_id',
        'created_by'
    ];

   
}
