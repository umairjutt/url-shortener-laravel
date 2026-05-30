<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment('Keep shortening.');
})->purpose('Display an inspiring quote');
