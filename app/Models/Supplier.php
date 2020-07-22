<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    public function orders()
    {
        return $this->hasMany('App\Models\Order');
    }

    public function projects()
    {
        return $this->belongsToMany('App\Models\Project', 'project_suppliers');
    }
}
