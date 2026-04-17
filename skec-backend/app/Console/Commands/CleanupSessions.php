<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Models\UserSession;
use Illuminate\Console\Command;

class CleanupSessions extends Command
{
    protected $signature   = 'skec:cleanup-sessions';
    protected $description = 'Mark inactive sessions where last_activity exceeds timeout';

    public function handle(): int
    {
        $timeoutMinutes = Setting::get('session_timeout_minutes', 60);
        $cutoff         = now()->subMinutes($timeoutMinutes);

        $count = UserSession::where('is_active', true)
            ->where('last_activity', '<', $cutoff)
            ->update(['is_active' => false]);

        $this->info("✓ Marked {$count} sessions as inactive (timeout: {$timeoutMinutes} min).");
        return Command::SUCCESS;
    }
}
