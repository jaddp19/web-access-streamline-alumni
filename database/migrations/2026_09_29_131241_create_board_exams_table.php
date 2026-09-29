<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('board_exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_profile_id')->constrained('user_profiles')->cascadeOnDelete();

            $table->string('exam_name')->nullable();
            $table->unsignedTinyInteger('attempt_number');
            $table->date('date_taken');
            $table->decimal('rate', 5, 2)->nullable();
            $table->boolean('passed')->default(false);

            // ── Per-attempt verification ─────────────────────
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            // ─────────────────────────────────────────────────

            $table->boolean('is_top_notcher')->default(false);
            $table->unsignedSmallInteger('top_notcher_rank')->nullable();
            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->unique(['user_profile_id', 'attempt_number']);
            $table->index(['user_profile_id', 'passed']);
            $table->index(['user_profile_id', 'is_top_notcher']);
            $table->index(['user_profile_id', 'is_verified']);  
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('board_exams');
    }
};