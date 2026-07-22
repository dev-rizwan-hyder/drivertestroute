<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrivingRoute extends Model
{
    protected $fillable = [
        'title',
        'package_type',
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
        'google_maps_url',
        'is_active',
    ];

    protected $casts = [
        'city_id' => 'integer',
        'package_type' => 'string',
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

    public function getGoogleMapsUrlAttribute(): string
    {
        if (! empty($this->attributes['google_maps_url'])) {
            return $this->attributes['google_maps_url'];
        }

        if ($this->start_lat !== null && $this->start_lng !== null) {
            $origin = "{$this->start_lat},{$this->start_lng}";
            $destination = ($this->end_lat !== null && $this->end_lng !== null)
                ? "{$this->end_lat},{$this->end_lng}"
                : $origin;

            $waypoints = [];
            if ($this->relationLoaded('points') && $this->points->count() > 0) {
                foreach ($this->points as $pt) {
                    if ($pt->lat !== null && $pt->lng !== null) {
                        $waypoints[] = "{$pt->lat},{$pt->lng}";
                    }
                }
            }

            $url = "https://www.google.com/maps/dir/?api=1&origin=" . urlencode($origin) . "&destination=" . urlencode($destination) . "&travelmode=driving";
            if (! empty($waypoints)) {
                $url .= "&waypoints=" . urlencode(implode('|', array_slice($waypoints, 0, 10)));
            }

            return $url;
        }

        if (! empty($this->start_label)) {
            $dest = ! empty($this->destination_label) ? $this->destination_label : $this->start_label;
            return "https://www.google.com/maps/dir/?api=1&origin=" . urlencode($this->start_label) . "&destination=" . urlencode($dest) . "&travelmode=driving";
        }

        return "https://www.google.com/maps";
    }

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
