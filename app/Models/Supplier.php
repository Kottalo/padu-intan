<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    // protected $appends = ['order_total', 'payment_total'];

    public function orders()
    {
        return $this->hasMany('App\Models\Order');
    }

    public function projects()
    {
        return $this->belongsToMany('App\Models\Project', 'project_suppliers');
    }

    // public function getOrderTotalAttribute()
    // {
    //     $total = 0;
    //
    //     $orders = Order::whereSupplierId($this->id)->get();
    //
    //     foreach ($orders as $order)
    //     {
    //         $total += $order->total;
    //     }
    //
    //     return $total;
    // }
    //
    // public function getPaymentTotalAttribute()
    // {
    //     $total = 0;
    //
    //     $orders = Order::whereSupplierId($this->id)->get();
    //
    //     foreach ($orders as $order)
    //     {
    //         $total += $order->payment->total;
    //     }
    //
    //     return $total;
    // }
}
