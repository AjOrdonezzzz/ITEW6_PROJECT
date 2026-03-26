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
        Schema::create('faculty_organizations', function (Blueprint $table) {
            $table->id('record_id');
            $table->foreignId('faculty_id')->constrained('faculty', 'faculty_id');
            $table->foreignId('organization_id')->constrained('organizations', 'organization_id');
            $table->string('role', 50)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faculty_organizations');
    }
};
