<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctypenameModel extends Model
{
    use HasFactory;

    protected $table = 'hms_document_typename';
    protected $fillable = [
    'doc_type',
     'doc_name',
     'status'
       ];

}