<?php

namespace App\Models;

use App\Models\product\ComboOffer;
use Illuminate\Database\Eloquent\Model;

class ComboPackItems extends Model
{
    protected $fillable = ['combo_pack_id', 'name', 'sort_order'];

    public function comboPack()
    {
        return $this->belongsTo(ComboOffer::class);
    }
}