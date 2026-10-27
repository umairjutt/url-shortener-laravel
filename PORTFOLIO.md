# Engineering guide

Laravel URL-shortening API with Redis caching, custom aliases, QR codes and click analytics.

## Code tour

- [app/Services/ShortLinkService.php](app/Services/ShortLinkService.php)
- [app/Http/Controllers/RedirectController.php](app/Http/Controllers/RedirectController.php)
- [app/Jobs/RecordClickJob.php](app/Jobs/RecordClickJob.php)
- [tests](tests)

## Scope and verification

Development API for Redis-backed redirects. Cached expiration, alias races and deployment assumptions require further validation.

[Current CI runs](https://github.com/umairjutt/url-shortener-laravel/actions). A workflow definition or build result does not establish that all runtime scenarios pass. The README contains development commands; unfinished setup and behavior need completion before production deployment.

## Maintainer

[Umair Jutt](https://github.com/umairjutt). Frameworks and dependencies retain their own ownership and licensing.
