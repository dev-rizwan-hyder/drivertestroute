<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoutePurchase extends Model
{
    protected $fillable = [
        'user_id',
        'driving_route_id',
        'payment_status',
        'payment_provider',
        'payment_id',
        'student_name',
        'student_email',
        'student_phone',
        'student_city',
        'student_test_date',
        'student_notes',
        'billing_name',
        'billing_email',
        'amount_paid',
        'access_limit',
        'access_used',
        'purchased_at',
        'last_accessed_at',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'access_limit' => 'integer',
        'access_used' => 'integer',
        'student_test_date' => 'date',
        'purchased_at' => 'datetime',
        'last_accessed_at' => 'datetime',
    ];

    public function route()
    {
        return $this->belongsTo(DrivingRoute::class, 'driving_route_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function remainingStarts(): int
    {
        return max(0, (int) $this->access_limit - (int) $this->access_used);
    }

    public function hasRemainingStarts(): bool
    {
        return $this->remainingStarts() > 0;
    }

    public function getRemainingStartsAttribute(): int
    {
        return $this->remainingStarts();
    }
}
