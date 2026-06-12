<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->enum('type', ['note', 'announcement'])->default('note')->after('message');
            $table->enum('announcement_type', ['info', 'warning', 'success', 'danger'])->nullable()->after('type');
            // Make note_id nullable to allow announcement-only notifications
            $table->foreignId('announcement_id')
                ->nullable()
                ->after('note_id')
                ->constrained('announcements')
                ->onDelete('cascade');
        });

        // Make note_id nullable (was required before)
        Schema::table('notifications', function (Blueprint $table) {
            $table->foreignId('note_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('announcement_id');
            $table->dropColumn(['type', 'announcement_type']);
        });
        Schema::table('notifications', function (Blueprint $table) {
            $table->foreignId('note_id')->nullable(false)->change();
        });
    }
};
