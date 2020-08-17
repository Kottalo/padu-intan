<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    public function payment()
    {
        return $this->hasMany('App\Models\Payment');
    }
}
