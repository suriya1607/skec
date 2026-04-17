<?php

namespace App\Console\Commands;

use App\Models\Invitation;
use Illuminate\Console\Command;

class CleanupInvitations extends Command
{
    protected $signature   = 'skec:cleanup-invitations';
    protected $description = 'Delete expired and unused invitations older than 7 days';

    public function handle(): int
    {
        $count = Invitation::whereNull('used_at')
            ->where('expires_at', '<', now()->subDays(7))
            ->delete();

        $this->info("✓ Cleaned up {$count} expired invitations.");
        return Command::SUCCESS;
    }
}
