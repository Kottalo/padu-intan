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

    public function payments()
    {
        return $this->hasMany('App\Models\Payment');
    }
}
