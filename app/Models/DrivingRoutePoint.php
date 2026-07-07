<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrivingRoutePoint extends Model
{
    protected $fillable = [
        'driving_route_id',
        'sort_order',
        'maneuver',
        'instruction',
        'lat',
        'lng',
        'distance_km',
        'duration',
    ];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
        'distance_km' => 'decimal:2',
    ];

    public function route()
    {
        return $this->belongsTo(DrivingRoute::class, 'driving_route_id');
    }
}
