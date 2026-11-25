<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Sincronización automática de tasa de cambio BOB / PEN (Ragnar Capital)
// Ejecución diaria a las 08:30, 13:00 y 18:00 (Zona horaria: America/La_Paz)
Schedule::command('tipo-cambio:actualizar')
    ->dailyAt('08:30')
    ->timezone('America/La_Paz')
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('tipo-cambio:actualizar')
    ->dailyAt('13:00')
    ->timezone('America/La_Paz')
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('tipo-cambio:actualizar')
    ->dailyAt('18:00')
    ->timezone('America/La_Paz')
    ->withoutOverlapping()
    ->runInBackground();

