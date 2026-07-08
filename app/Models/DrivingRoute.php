<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrivingRoute extends Model
{
    protected $fillable = [
        'title',
        'city_id',
        'city',
        'province',
        'description',
        'route_duration_minutes',
        'route_length_km',
        'start_label',
        'start_lat',
        'start_lng',
        'destination_label',
        'end_lat',
        'end_lng',
        'price',
        'access_limit',
        'preview_pdf_path',
        'is_active',
    ];

    protected $casts = [
        'city_id' => 'integer',
        'start_lat' => 'float',
        'start_lng' => 'float',
        'end_lat' => 'float',
        'end_lng' => 'float',
        'route_duration_minutes' => 'integer',
        'route_length_km' => 'decimal:2',
        'price' => 'decimal:2',
        'access_limit' => 'integer',
        'is_active' => 'boolean',
    ];

    public function points()
    {
        return $this->hasMany(DrivingRoutePoint::class)->orderBy('sort_order');
    }

    public function cityModel()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function purchases()
    {
        return $this->hasMany(RoutePurchase::class);
    }

    public function activePurchaseFor($user): ?RoutePurchase
    {
        if (!$user) {
            return null;
        }

        return $this->purchases()
            ->where('user_id', $user->id)
            ->where('payment_status', 'paid')
            ->first();
    }

    public function isPurchasedBy($user)
    {
        return $this->activePurchaseFor($user) !== null;
    }
}
