<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectSupplier extends Model
{
    public function projects()
    {
        return $this->hasMany('App\Models\Project');
    }

    public function suppliers()
    {
        return $this->hasMany('App\Models\Supplier');
    }
}
