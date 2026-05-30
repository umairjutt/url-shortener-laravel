<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShortLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'destination_url',
        'expires_at',
        'click_count',
        'created_ip',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'click_count' => 'integer',
    ];

    public function clicks(): HasMany
    {
        return $this->hasMany(LinkClick::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function shortUrl(): string
    {
        return rtrim(config('app.url'), '/') . '/' . $this->slug;
    }
}
