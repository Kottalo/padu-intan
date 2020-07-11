<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    public function order_items()
    {
        return $this->hasMany('App\Models\OrderItem');
    }
}
