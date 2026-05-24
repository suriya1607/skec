<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['category_id']);
        });

        Schema::table('notes', function (Blueprint $table) {
            // Change column to string to hold comma-separated category IDs
            $table->string('category_id', 255)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->change();
            $table->foreign('category_id')->references('id')->on('note_categories')->onDelete('set null');
        });
    }
};
