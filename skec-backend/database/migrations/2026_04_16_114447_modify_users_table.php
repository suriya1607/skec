<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Users table is defined in 0001_01_01_000000_create_users_table.php
// This migration is intentionally empty - all user fields are in the original migration
return new class extends Migration
{
    public function up(): void
    {
        // All user fields already added to users table in the create migration
    }

    public function down(): void
    {
        //
    }
};
