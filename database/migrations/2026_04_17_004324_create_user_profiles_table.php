<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('avatar')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('contact_number_1');
            $table->string('contact_number_2')->nullable();
            $table->json('location');
            $table->foreignId('batch_id')->constrained('batches')->cascadeOnDelete();
            $table->boolean('is_private');

            // Cached mirror of the alumni's featured board attempt
            // (see BoardExam model + UserProfile::syncPrimaryBoardAttempt()).
            // Updated automatically whenever a BoardExam row is added/edited/deleted.
            // Never write to these directly — always go through the sync helper.
            $table->date('board_taken')->nullable();
            $table->decimal('board_rate', 5, 2)->nullable();

            $table->boolean('is_verified')->default(false);

            // Registrar approval workflow
            $table->boolean('is_approved')->default(false);
            $table->text('last_rejection_reason')->nullable();

            // Notification preferences
            $table->boolean('email_notifications')->default(true);

            $table->timestamps();

            $table->index('is_approved');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};