<?php

use Illuminate\Foundation\Console\ClosureCommand;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function (ClosureCommand $command) {
    $command->comment('Suivez le groupe, pas seulement le chrono.');
})->purpose('Display an inspiring quote');
