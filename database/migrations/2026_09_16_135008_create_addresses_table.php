<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('ph_address')->create('Addresses', function (Blueprint $table) {
            $table->string('code', 12)->primary();
            $table->string('name', 255);
            $table->string('level', 10)->index();
            $table->string('parent_code', 12)->nullable()->index();
        });

        DB::connection('ph_address')->statement('CREATE INDEX idx_code_level ON Addresses(code, level)');
    }

    public function down(): void
    {
        Schema::connection('ph_address')->dropIfExists('Addresses');
    }
};