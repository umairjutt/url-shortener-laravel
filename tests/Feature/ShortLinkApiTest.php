<?php

use App\Models\ShortLink;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

test('can create short link with auto-generated slug', function () {
    $response = $this->postJson('/api/links', [
        'url' => 'https://laravel.com',
    ]);

    $response->assertCreated()
        ->assertJsonStructure(['slug', 'short_url', 'destination_url']);

    expect(ShortLink::count())->toBe(1);
});

test('can create short link with custom alias', function () {
    $response = $this->postJson('/api/links', [
        'url' => 'https://example.com',
        'alias' => 'mylink',
    ]);

    $response->assertCreated()
        ->assertJsonPath('slug', 'mylink');
});

test('cannot use reserved alias', function () {
    $response = $this->postJson('/api/links', [
        'url' => 'https://example.com',
        'alias' => 'admin',
    ]);

    $response->assertUnprocessable();
});

test('cannot create duplicate alias', function () {
    ShortLink::create([
        'slug' => 'taken',
        'destination_url' => 'https://a.com',
    ]);

    $response = $this->postJson('/api/links', [
        'url' => 'https://b.com',
        'alias' => 'taken',
    ]);

    $response->assertUnprocessable();
});

test('redirect dispatches click job and returns 302', function () {
    Queue::fake();

    $link = ShortLink::create([
        'slug' => 'go',
        'destination_url' => 'https://laravel.com',
    ]);

    $this->get('/go')->assertRedirect('https://laravel.com');

    Queue::assertPushed(\App\Jobs\RecordClickJob::class);
});

test('expired link returns 404', function () {
    ShortLink::create([
        'slug' => 'old',
        'destination_url' => 'https://example.com',
        'expires_at' => now()->subDay(),
    ]);

    $this->get('/old')->assertNotFound();
});
