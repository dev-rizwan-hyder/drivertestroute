<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'category',
        'excerpt',
        'body',
        'featured_image',
        'read_time',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (BlogPost $post) {
            $slug = Str::slug($post->title);
            $original = $slug;
            $count = 1;

            while (static::where('slug', $slug)->exists()) {
                $slug = $original . '-' . $count;
                $count++;
            }

            $post->slug = $slug;
        });
    }

    /**
     * Scope to only published posts.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->where('published_at', '<=', now());
    }
}
