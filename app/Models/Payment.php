<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $appends = ['total'];

    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }

    public function getTotalAttribute()
    {
        return $this->cheque + $this->cash + $this->online;
    }
}
