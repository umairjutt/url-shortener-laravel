<?php

namespace Database\Seeders;

use App\Models\ShortLink;
use App\Support\Base62;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $seeds = [
            ['url' => 'https://laravel.com', 'alias' => 'laravel'],
            ['url' => 'https://github.com', 'alias' => 'github'],
            ['url' => 'https://stackoverflow.com', 'alias' => null],
        ];

        foreach ($seeds as $seed) {
            $link = ShortLink::create([
                'slug' => $seed['alias'] ?? 'tmp' . uniqid(),
                'destination_url' => $seed['url'],
                'click_count' => 0,
            ]);

            if ($seed['alias'] === null) {
                $link->slug = Base62::fromId($link->id);
                $link->save();
            }
        }
    }
}
