<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE access_logs MODIFY action ENUM('opened', 'closed', 'page_changed', 'screenshot_attempt', 'capture_attempt', 'print_attempt', 'copy_attempt') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE access_logs MODIFY action ENUM('opened', 'closed', 'page_changed') NOT NULL");
    }
};
