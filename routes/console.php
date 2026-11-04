<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Recordatorio diario de membresías por vencer (requiere el cron del scheduler en el servidor:
//   * * * * * cd /ruta/al/proyecto && php artisan schedule:run >> /dev/null 2>&1 )
Schedule::command('socios:recordar-vencimientos')->dailyAt('09:00');
