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
        Schema::create('student_violations', function (Blueprint $table) {
            $table->id('violation_id');
            $table->foreignId('student_id')->constrained('students', 'student_id');
            $table->foreignId('violation_type_id')->constrained('violation_types', 'violation_type_id');
            $table->date('violation_date');
            $table->text('description')->nullable();
            $table->string('action_taken', 100)->nullable();
            $table->string('status', 20);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_violations');
    }
};
