<?php

namespace App\Console\Commands;

use App\Models\AccessLog;
use Illuminate\Console\Command;

class CleanupActivityLogs extends Command
{
    protected $signature = 'skec:cleanup-activity-logs {--days=90 : Delete activity logs older than this many days}';
    protected $description = 'Delete old activity/access logs after the retention period';

    public function handle(): int
    {
        $days = max(1, (int) $this->option('days'));
        $cutoff = now()->subDays($days);

        $count = AccessLog::where('created_at', '<', $cutoff)->delete();

        $this->info("✓ Cleaned up {$count} activity logs older than {$days} days.");
        return Command::SUCCESS;
    }
}
