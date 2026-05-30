<?php

namespace App\Jobs;

use App\Models\LinkClick;
use App\Models\ShortLink;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Jenssegers\Agent\Agent;

class RecordClickJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $shortLinkId,
        public string $ip,
        public ?string $userAgent,
        public ?string $referer,
        public ?string $country,
        public string $clickedAt,
    ) {}

    public function handle(): void
    {
        $agent = new Agent();
        $agent->setUserAgent($this->userAgent ?? '');

        $deviceType = match (true) {
            $agent->isMobile() => 'mobile',
            $agent->isTablet() => 'tablet',
            $agent->isDesktop() => 'desktop',
            default => 'unknown',
        };

        LinkClick::create([
            'short_link_id' => $this->shortLinkId,
            'ip' => $this->ip,
            'user_agent' => $this->userAgent,
            'country' => $this->country,
            'device_type' => $deviceType,
            'browser' => $agent->browser() ?: null,
            'referer' => $this->referer,
            'clicked_at' => $this->clickedAt,
        ]);

        ShortLink::where('id', $this->shortLinkId)->increment('click_count');
    }
}
