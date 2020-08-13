<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OrderItem;

class Order extends Model
{
    // protected $appends = [
    //   'sst_amount',
    //   'total',
    //   'sub_total',
    // ];

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

    public function payment()
    {
        return $this->hasOne('App\Models\Payment');
    }

    // public function getTotalAttribute()
    // {
    //     $order_items = $this->order_items;
    //
    //     $total = 0;
    //
    //     foreach ($order_items as $order_item)
    //     {
    //         $total += $order_item->total;
    //     }
    //
    //     return $total;
    // }
    //
    // public function getSstAmountAttribute()
    // {
    //     $order_items = $this->order_items;
    //
    //     $total = 0;
    //
    //     foreach ($order_items as $order_item)
    //     {
    //         $total += $order_item->sst_amount;
    //     }
    //
    //     return $total;
    // }
    //
    // public function getSubTotalAttribute()
    // {
    //     $order_items = $this->order_items;
    //
    //     $total = 0;
    //
    //     foreach ($order_items as $order_item)
    //     {
    //         $value = $order_item->sub_total;
    //         $total += $order_item->return ? -$value : $value;
    //     }
    //
    //     return $total;
    // }
}
