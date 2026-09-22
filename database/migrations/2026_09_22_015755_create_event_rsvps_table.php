<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_rsvps', function (Blueprint $table) {
            $table->id();

            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->enum('response', ['yes', 'no', 'maybe'])->default('yes');
            $table->timestamp('responded_at')->useCurrent();

            $table->timestamp('attended_at')->nullable();
            $table->string('notes', 500)->nullable();

            $table->timestamps();

            $table->unique(['event_id', 'user_id']);
            $table->index(['event_id', 'response']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_rsvps');
    }
};