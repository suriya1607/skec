<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('skec:cleanup-invitations')->dailyAt('00:00');
Schedule::command('skec:cleanup-sessions')->everyThirtyMinutes();
