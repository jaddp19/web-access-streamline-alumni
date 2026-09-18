<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::connection('ph_address')->hasTable('Addresses')) {
            return;
        }

        Schema::connection('ph_address')->create('Addresses', function (Blueprint $table) {
            $table->string('code', 12)->primary();
            $table->string('name', 255);
            $table->string('level', 10)->index();
            $table->string('parent_code', 12)->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::connection('ph_address')->dropIfExists('Addresses');
    }
};