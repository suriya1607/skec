<?php

namespace App\Console\Commands;

use App\Models\Notification;
use Illuminate\Console\Command;

class PruneOldNotifications extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'notifications:prune
                            {--days=7 : Number of days after which unread notifications are deleted}';

    /**
     * The console command description.
     */
    protected $description = 'Delete unread notifications older than the specified number of days (default: 7)';

    public function handle(): int
    {
        $days = (int) $this->option('days');

        $cutoff = now()->subDays($days);

        $deleted = Notification::where('is_read', false)
            ->where('created_at', '<', $cutoff)
            ->delete();

        $this->info("Pruned {$deleted} unread notification(s) older than {$days} day(s).");

        return self::SUCCESS;
    }
}
