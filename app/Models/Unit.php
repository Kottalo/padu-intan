<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    public function order_items()
    {
        return $this->hasMany('App\Models\OrderItem');
    }
}
