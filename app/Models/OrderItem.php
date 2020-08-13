<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    // protected $appends = [
    //   'sst_amount',
    //   'total',
    //   'sub_total',
    // ];

    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }

    public function item()
    {
        return $this->belongsTo('App\Models\Item');
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit');
    }

    // public function getTotalAttribute($value)
    // {
    //     return $this->attributes['quantity'] * $this->attributes['price'];
    // }
    //
    // public function getSstAmountAttribute($value)
    // {
    //     return $this->total * $this->sst_perc;
    // }
    //
    // public function getSubTotalAttribute($value)
    // {
    //     return $this->total + $this->sst_amount;
    // }
}
