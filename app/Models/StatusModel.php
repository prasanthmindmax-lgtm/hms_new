<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusModel extends Model
{
    use HasFactory;
    protected $table = 'ticket_status_master';
    protected $fillable = [
        'status_name',
        'status_color'
    ];

}
