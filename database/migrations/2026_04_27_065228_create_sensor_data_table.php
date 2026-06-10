<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sensor_data', function (Blueprint $table) {
            $table->id();
            $table->float('soil_moisture');     // Kelembaban tanah (%)
            $table->float('soil_temperature');  // Suhu tanah (°C)
            $table->float('ldr_value');         // Intensitas cahaya dari LDR 2 kaki (%)
            $table->float('tds_value');         // TDS/Nutrisi (ppm)
            $table->boolean('pump_status')->default(false); // Status pompa
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sensor_data');
    }
};
