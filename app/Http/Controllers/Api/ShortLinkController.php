<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ShortLinkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ShortLinkController extends Controller
{
    public function __construct(private readonly ShortLinkService $service) {}

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'url' => ['required', 'url', 'max:2048'],
            'alias' => ['nullable', 'alpha_dash', 'min:3', 'max:32'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ]);

        $link = $this->service->create(
            url: $data['url'],
            alias: $data['alias'] ?? null,
            expiresAt: $data['expires_at'] ?? null,
            ip: $request->ip(),
        );

        return response()->json([
            'slug' => $link->slug,
            'short_url' => $link->shortUrl(),
            'destination_url' => $link->destination_url,
            'expires_at' => $link->expires_at,
        ], Response::HTTP_CREATED);
    }

    public function show(string $slug): JsonResponse
    {
        $link = $this->service->find($slug);

        if ($link === null) {
            return response()->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'slug' => $link->slug,
            'short_url' => $link->shortUrl(),
            'destination_url' => $link->destination_url,
            'click_count' => $link->click_count,
            'created_at' => $link->created_at,
            'expires_at' => $link->expires_at,
        ]);
    }

    public function qr(string $slug)
    {
        $link = $this->service->find($slug);

        if ($link === null) {
            return response()->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        $png = QrCode::format('png')->size(300)->margin(1)->generate($link->shortUrl());

        return response($png, 200, ['Content-Type' => 'image/png']);
    }
}
