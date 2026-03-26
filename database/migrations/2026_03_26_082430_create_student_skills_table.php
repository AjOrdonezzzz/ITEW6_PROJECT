<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_skills', function (Blueprint $table) {
            $table->id('record_id');
            $table->foreignId('student_id')->constrained('students', 'student_id');
            $table->foreignId('skill_id')->constrained('skills', 'skill_id');
            $table->string('skill_level', 50)->nullable();
            $table->string('certification', 100)->nullable();
            $table->date('date_acquired')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_skills');
    }
};
