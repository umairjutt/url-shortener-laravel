<?php

return [
    'link_ttl_days' => env('SHORTENER_LINK_TTL_DAYS', 365),
    'rate_limit_per_min' => env('SHORTENER_RATE_LIMIT_PER_MIN', 5),
    'cache_ttl_seconds' => 3600,
    'reserved_aliases' => ['api', 'admin', 'login', 'logout', 'register', 'docs', 'up'],
];
