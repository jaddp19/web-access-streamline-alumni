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
        Schema::create('civil_status_employments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tracer_study_id')->constrained('tracer_studies')->cascadeOnDelete()->unique();

            $table->enum('civil_status', ['single', 'married', 'widowed', 'separated', 'single-parent']);
            $table->enum('employment_status', ['employed', 'unemployed', 'self-employed', 'other']);
            $table->string('current_job_position')->nullable();
            $table->enum('employed_related_to_degree', ['yes', 'no', 'partially-related'])->nullable();
            $table->enum('employment_type', ['full-time', 'part-time', 'contractual-project-based', 'freelance', 'other'])->nullable();
            $table->enum('organization_type', ['private-company', 'government-agency', 'non-government-organization', 'educational-institution', 'self-employed-business', 'other'])->nullable();
            $table->enum('employment_area', ['philippines', 'abroad'])->nullable();
            $table->string('abroad_country')->nullable();
            $table->enum('months_to_first_job', ['1-3-months', '4-6-months', 'more-than-6-months', 'more-than-1-year', 'not-yet-employed'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('civil_status_employments');
    }
};
