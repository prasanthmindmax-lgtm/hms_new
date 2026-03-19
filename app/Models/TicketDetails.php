<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketDetails extends Model
{
    use HasFactory;

    protected $table = 'tbl_ticket_details';
    protected $fillable = [
      'ticket_no',
     'employee_name',
      'location_id',
      'from_department_id',
       'department_id',
        'sub_department_id',
         'status',
         'subject',
         'description',
         'priority',
         'target_date',
         'image_paths',
         'created_by',
         'ticket_status',
         'is_read',
         'zone_id',
         'created_at',
         'updated_at',
        
        ];
}
