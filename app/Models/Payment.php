<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $appends = [
      'total',
      'paid_total',
    ];

    public function project()
    {
        return $this->belongsTo('App\Models\Project');
    }

    public function supplier()
    {
        return $this->belongsTo('App\Models\Supplier');
    }

    public function bank()
    {
        return $this->belongsTo('App\Models\Bank');
    }

    public function getTotalAttribute()
    {
        $total = 0;

        $orders = Order::where([
          'project_id' => $this->project_id,
          'supplier_id' => $this->supplier_id,
        ])
        ->whereRaw('YEAR(date) = ?', [$this->year])
        ->whereRaw('MONTH(date) = ?', [$this->month])
        ->get();

        foreach ($orders as $order)
        {
            $total += $order->total;
        }

        return $total;
    }

    public function getPaidTotalAttribute()
    {
        return $this->cheque + $this->cash + $this->online;
    }
}
