<?php

namespace App\Services;

use App\Models\ShortLink;
use App\Support\Base62;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ShortLinkService
{
    public function create(string $url, ?string $alias = null, ?string $expiresAt = null, ?string $ip = null): ShortLink
    {
        if ($alias !== null) {
            $this->validateAlias($alias);
        }

        return DB::transaction(function () use ($url, $alias, $expiresAt, $ip) {
            $link = ShortLink::create([
                'slug' => $alias ?? '__pending__' . uniqid(),
                'destination_url' => $url,
                'expires_at' => $expiresAt,
                'created_ip' => $ip,
                'click_count' => 0,
            ]);

            if ($alias === null) {
                $link->slug = Base62::fromId($link->id);
                $link->save();
            }

            $this->cacheUrl($link->slug, $link->destination_url);

            return $link;
        });
    }

    public function resolve(string $slug): ?string
    {
        $cacheKey = $this->cacheKey($slug);

        return Cache::remember($cacheKey, config('shortener.cache_ttl_seconds', 3600), function () use ($slug) {
            $link = ShortLink::where('slug', $slug)->first();

            if ($link === null || $link->isExpired()) {
                return null;
            }

            return $link->destination_url;
        });
    }

    public function find(string $slug): ?ShortLink
    {
        return ShortLink::where('slug', $slug)->first();
    }

    private function validateAlias(string $alias): void
    {
        if (in_array($alias, config('shortener.reserved_aliases', []), true)) {
            throw ValidationException::withMessages(['alias' => 'Alias is reserved.']);
        }

        if (ShortLink::where('slug', $alias)->exists()) {
            throw ValidationException::withMessages(['alias' => 'Alias already taken.']);
        }
    }

    private function cacheUrl(string $slug, string $url): void
    {
        Cache::put($this->cacheKey($slug), $url, config('shortener.cache_ttl_seconds', 3600));
    }

    private function cacheKey(string $slug): string
    {
        return "shortlink:resolve:{$slug}";
    }
}
