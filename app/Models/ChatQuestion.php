<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatQuestion extends Model
{
    protected $fillable = [
        'question',
        'answer',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public static function getActive()
    {
        return self::where('is_active', true)
            ->orderBy('order')
            ->get();
    }
}

