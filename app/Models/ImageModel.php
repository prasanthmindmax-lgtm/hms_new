<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TicketModel;

class ImageModel extends Model
{
    use HasFactory;
    protected $table = 'image_upload_tables';
    protected $fillable = [
        'imgName',
        'path',
        'url',
        'ticket_id'
    ];
}
