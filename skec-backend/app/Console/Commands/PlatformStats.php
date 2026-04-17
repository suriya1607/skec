<?php

namespace App\Console\Commands;

use App\Models\AccessLog;
use App\Models\Invitation;
use App\Models\Note;
use App\Models\User;
use App\Models\UserSession;
use Illuminate\Console\Command;

class PlatformStats extends Command
{
    protected $signature   = 'skec:stats';
    protected $description = 'Display SKEC platform statistics';

    public function handle(): int
    {
        $this->info('═══════════════════════════════════════');
        $this->info('   Sri Kumaran Education Centre Stats  ');
        $this->info('═══════════════════════════════════════');

        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Students',      User::students()->count()],
                ['Active Students',     User::students()->active()->count()],
                ['Total Notes',         Note::count()],
                ['Published Notes',     Note::published()->count()],
                ['Draft Notes',         Note::draft()->count()],
                ['Active Sessions',     UserSession::active()->count()],
                ['Pending Invitations', Invitation::pending()->count()],
                ['Total Access Logs',   AccessLog::count()],
            ]
        );

        return Command::SUCCESS;
    }
}
