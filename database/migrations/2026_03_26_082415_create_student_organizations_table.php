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
        Schema::create('student_organizations', function (Blueprint $table) {
            $table->id('record_id');
            $table->foreignId('student_id')->constrained('students', 'student_id');
            $table->foreignId('organization_id')->constrained('organizations', 'organization_id');
            $table->string('role', 50)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status', 20);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_organizations');
    }
};
