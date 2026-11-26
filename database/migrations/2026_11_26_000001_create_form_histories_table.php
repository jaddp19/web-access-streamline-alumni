<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_histories', function (Blueprint $table) {
            $table->id();

            // The alumni who owns this accepted form.
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // The Program Head / Alumni Coordinator who accepted it.
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();

            // 1st accepted form, 2nd accepted form, ...
            $table->unsignedInteger('version')->default(1);

            $table->timestamp('approved_at')->nullable();

            // Immutable copy of everything the alumni submitted. The live
            // profile/tracer/work rows are edited in place, so history cannot
            // safely point at them with foreign keys.
            $table->json('snapshot');

            // Lets approve() stay idempotent if it is triggered twice without
            // any data changing in between.
            $table->string('snapshot_hash', 64);

            $table->timestamps();

            $table->unique(['user_id', 'version']);
            $table->index(['user_id', 'approved_at']);
            $table->index('snapshot_hash');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_histories');
    }
};
