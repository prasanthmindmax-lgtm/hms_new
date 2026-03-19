<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketChat extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
        'file_path',
    ];

    // Relationship with TicketDetails
    public function ticket()
    {
        return $this->belongsTo(TicketDetails::class, 'ticket_id');
    }

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Get file URL accessor
    public function getFileUrlAttribute()
    {
        return $this->file_path ? asset($this->file_path) : null;
    }
    public function sender()
    {
        return $this->belongsTo(User::class, 'user_id'); // user_id is the foreign key
    }
}
