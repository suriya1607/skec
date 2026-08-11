<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // First, drop the foreign key constraint on course_id
        Schema::table('student_profiles', function (Blueprint $table) {
            // Drop FK constraint — name follows Laravel convention
            $table->dropForeign(['course_id']);
        });

        // Change the column from unsignedBigInteger to string to support comma-separated IDs
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->string('course_id', 500)->change();
        });
    }

    public function down(): void
    {
        // Revert: change back to foreign key integer
        // Note: data with multiple IDs will be truncated to the first value
        DB::statement('UPDATE student_profiles SET course_id = SUBSTRING_INDEX(course_id, \',\', 1) WHERE course_id LIKE \'%,%\'');

        Schema::table('student_profiles', function (Blueprint $table) {
            $table->unsignedBigInteger('course_id')->change();
        });

        Schema::table('student_profiles', function (Blueprint $table) {
            $table->foreign('course_id')->references('id')->on('note_categories')->restrictOnDelete();
        });
    }
};
