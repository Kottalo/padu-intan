<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }

    public function orders()
    {
        return $this->hasMany('App\Models\Order');
    }

    public function suppliers()
    {
        return $this->belongsToMany('App\Models\Supplier', 'project_suppliers');
    }

    public function payments()
    {
        return $this->hasMany('App\Models\Payment');
    }
}
