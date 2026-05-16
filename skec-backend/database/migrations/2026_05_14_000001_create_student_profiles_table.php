<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('reg_no')->unique();
            $table->string('father_name');
            $table->date('dob');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->text('address');
            $table->string('community_category', 20);
            $table->string('contact_phone', 20);
            $table->string('qualification');
            $table->foreignId('course_id')->constrained('note_categories')->restrictOnDelete();
            $table->enum('medium_of_studying', ['english', 'tamil']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
};
