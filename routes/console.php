<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Vérifie chaque jour à 8h les projets dont la date limite tombe dans
// exactement 3 jours, et envoie un mail de rappel à leur propriétaire.
Schedule::command('reminders:send-project-deadlines')->dailyAt('08:00');