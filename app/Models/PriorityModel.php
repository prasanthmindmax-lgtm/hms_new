<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriorityModel extends Model
{
    use HasFactory;

    protected $table = 'ticket_priority';
    protected $fillable = [
        'priority_name',
        'priority_color',
    ];
}
