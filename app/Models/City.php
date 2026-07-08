<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        'name',
        'address',
    ];

    public function routes()
    {
        return $this->hasMany(DrivingRoute::class);
    }
}
