<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function project()
    {
        return $this->belongsTo('App\Models\Project');
    }

    public function supplier()
    {
        return $this->belongsTo('App\Models\Supplier');
    }

    public function order_items()
    {
        return $this->hasMany('App\Models\OrderItem');
    }
}
