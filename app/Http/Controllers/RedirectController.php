<?php

namespace App\Http\Controllers;

use App\Jobs\RecordClickJob;
use App\Models\ShortLink;
use App\Services\ShortLinkService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RedirectController extends Controller
{
    public function __construct(private readonly ShortLinkService $service) {}

    public function __invoke(Request $request, string $slug): RedirectResponse|Response
    {
        $url = $this->service->resolve($slug);

        if ($url === null) {
            return response('Short link not found or expired.', Response::HTTP_NOT_FOUND);
        }

        $link = ShortLink::where('slug', $slug)->first(['id']);
        if ($link !== null) {
            RecordClickJob::dispatch(
                shortLinkId: $link->id,
                ip: $request->ip(),
                userAgent: $request->userAgent(),
                referer: $request->headers->get('referer'),
                country: $request->headers->get('cf-ipcountry'),
                clickedAt: now()->toDateTimeString(),
            );
        }

        return redirect()->away($url, Response::HTTP_FOUND);
    }
}
