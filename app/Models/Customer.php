<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    
  protected $fillable = [
        'mobile',
        'name',
        'email',
        'address',
        'city',
        'state',
        'pincode',
    ];

    public function orders()
    {
        return $this->hasMany(Orders::class);
    }
}
