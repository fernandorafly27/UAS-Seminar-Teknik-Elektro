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
        Schema::create('sensor_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('soil_humidity', 5, 1);
            $table->decimal('soil_temperature', 5, 1);
            $table->unsignedTinyInteger('ldr_value');
            $table->unsignedSmallInteger('tds_value');
            $table->boolean('pump_status')->default(false);
            $table->dateTime('rtc_time')->nullable();
            $table->dateTime('recorded_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensor_readings');
    }
};
