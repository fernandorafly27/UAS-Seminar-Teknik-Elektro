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
        Schema::create('system_controls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('control_name'); // Nama kontrol (e.g., "Mode Otomatis")
            $table->string('control_key')->nullable();
            $table->boolean('is_active')->default(false); // Status kontrol
            $table->string('description')->nullable(); // Deskripsi
            $table->dateTime('last_triggered_at')->nullable();
            $table->timestamps();
            $table->index('user_id');
            $table->unique(['user_id', 'control_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_controls');
    }
};
