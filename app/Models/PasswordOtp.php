<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordOtp extends Model
{
    protected $fillable = ['user_id','otp','expires_at', 'attempts'];
    protected $dates = ['expires_at', 'created_at', 'updated_at'];
    protected $casts = [
        'expires_at' => 'datetime',
        'locked_until' => 'datetime',
    ];

}
