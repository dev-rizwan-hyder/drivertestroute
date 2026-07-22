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
        $customUrl = trim($this->attributes['google_maps_url'] ?? '');

        // If customUrl is ALREADY a Google Maps Directions URL, use it directly!
        if ($customUrl && (str_contains($customUrl, '/maps/dir/') || str_contains($customUrl, 'api=1'))) {
            if (! str_contains($customUrl, 'dir_action=')) {
                $customUrl .= (str_contains($customUrl, '?') ? '&' : '?') . 'dir_action=navigate';
            }
            return $customUrl;
        }

        $origin = null;
        $destination = null;
        $waypoints = [];

        if ($this->start_lat !== null && $this->start_lng !== null && is_numeric($this->start_lat)) {
            $origin = "{$this->start_lat},{$this->start_lng}";
        } elseif (! empty($this->start_label) && ! $this->isGenericLabel($this->start_label)) {
            $origin = $this->start_label . ($this->city ? ", {$this->city}" : '');
        } elseif ($this->city) {
            $origin = $this->city . ($this->province ? ", {$this->province}" : '');
        }

        if ($this->end_lat !== null && $this->end_lng !== null && is_numeric($this->end_lat)) {
            $destination = "{$this->end_lat},{$this->end_lng}";
        } elseif (! empty($this->destination_label) && ! $this->isGenericLabel($this->destination_label)) {
            $destination = $this->destination_label . ($this->city ? ", {$this->city}" : '');
        } else {
            $destination = $origin;
        }

        if ($this->relationLoaded('points') && $this->points->count() > 0) {
            foreach ($this->points as $pt) {
                if ($pt->lat !== null && $pt->lng !== null && is_numeric($pt->lat)) {
                    $waypoints[] = "{$pt->lat},{$pt->lng}";
                } elseif (! empty($pt->instruction) && ! $this->isGenericLabel($pt->instruction)) {
                    $cleanInst = preg_replace('/^(continue|turn left|turn right) onto /i', '', $pt->instruction);
                    if ($cleanInst && ! $this->isGenericLabel($cleanInst)) {
                        $waypoints[] = $cleanInst . ($this->city ? ", {$this->city}" : '');
                    }
                }
            }
        }

        if ($origin && $destination) {
            $url = "https://www.google.com/maps/dir/?api=1&origin=" . urlencode($origin) . "&destination=" . urlencode($destination) . "&travelmode=driving&dir_action=navigate";
            if (! empty($waypoints)) {
                $midWaypoints = array_slice($waypoints, 0, 9);
                $url .= "&waypoints=" . urlencode(implode('|', $midWaypoints));
            }

            return $url;
        }

        if ($customUrl !== '') {
            return $customUrl;
        }

        return "https://www.google.com/maps";
    }

    private function isGenericLabel(?string $label): bool
    {
        if (! $label) {
            return true;
        }
        $lower = strtolower(trim($label));
        return in_array($lower, [
            'midpoint', 
            'return to start', 
            'midpoint / return to start', 
            'destination', 
            'start', 
            'start point', 
            'end point', 
            'waypoint',
            'your location',
        ]);
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
