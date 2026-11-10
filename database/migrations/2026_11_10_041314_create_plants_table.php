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
        Schema::create('plants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('species')->nullable();
            $table->string('location')->nullable();
            $table->string('pot_size')->nullable();
            $table->unsignedInteger('daily_base_ml')->default(100);
            $table->unsignedSmallInteger('default_interval_days')->default(7);
            $table->string('photo_path')->nullable();
            $table->boolean('is_demo')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plants');
    }
};
