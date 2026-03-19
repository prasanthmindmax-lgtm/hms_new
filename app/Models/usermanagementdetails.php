<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class usermanagementdetails extends Model
{
    use HasFactory;

    protected $table = 'users';
    protected $fillable = [
      'user_fullname',
      'username',
        'password',
        'role_id',
          'email',
     ];


}
