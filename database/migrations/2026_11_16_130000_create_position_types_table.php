<?php

use Database\Seeders\PositionTypeSeeder;
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
        Schema::create('position_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('category', 100)->index();
            $table->text('default_description')->nullable();
            $table->text('default_requirements')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Katalog bawaan harus langsung tersedia di setiap environment setelah migrate.
        (new PositionTypeSeeder)->run();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('position_types');
    }
};
