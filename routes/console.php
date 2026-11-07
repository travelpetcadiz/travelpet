<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Privacidad: borra cada noche los datos que ya no hace falta guardar
Schedule::command('travelpet:limpiar-datos')->dailyAt('04:00');
