<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class AppUser extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'app_users';

    protected $fillable = [
        'mobile',
        'name',
        'email',
    ];
}
