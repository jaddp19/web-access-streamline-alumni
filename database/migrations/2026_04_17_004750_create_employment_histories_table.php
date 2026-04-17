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
        Schema::create('employment_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumni_profile_id')->constrained('alumni_profiles')->cascadeOnDelete();
            $table->string('company_name');
            $table->string('job_title');
            $table->enum('employment_sector', ['education', 'healthcare', 'technology', 'business', 'government', 'manufacturing', 'hospitality', 'agriculture', 'others']);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_current')->defaut(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employment_histories');
    }
};
