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
        Schema::create('faculty_subjects', function (Blueprint $table) {
            $table->id('record_id');
            $table->foreignId('faculty_id')->constrained('faculty', 'faculty_id');
            $table->foreignId('subject_id')->constrained('subjects', 'subject_id');
            $table->string('school_year', 20);
            $table->string('semester', 20);
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faculty_subjects');
    }
};
