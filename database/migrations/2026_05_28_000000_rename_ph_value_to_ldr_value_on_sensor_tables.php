<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sensor_readings') && Schema::hasColumn('sensor_readings', 'ph_value')) {
            Schema::table('sensor_readings', function (Blueprint $table) {
                $table->renameColumn('ph_value', 'ldr_value');
            });

            DB::table('sensor_readings')
                ->where('ldr_value', '<=', 14)
                ->update(['ldr_value' => DB::raw('ROUND(ldr_value * 10)')]);
        }

        if (Schema::hasTable('sensor_data') && Schema::hasColumn('sensor_data', 'ph_level')) {
            Schema::table('sensor_data', function (Blueprint $table) {
                $table->renameColumn('ph_level', 'ldr_value');
            });

            DB::table('sensor_data')
                ->where('ldr_value', '<=', 14)
                ->update(['ldr_value' => DB::raw('ROUND(ldr_value * 10)')]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('sensor_readings') && Schema::hasColumn('sensor_readings', 'ldr_value')) {
            Schema::table('sensor_readings', function (Blueprint $table) {
                $table->renameColumn('ldr_value', 'ph_value');
            });
        }

        if (Schema::hasTable('sensor_data') && Schema::hasColumn('sensor_data', 'ldr_value')) {
            Schema::table('sensor_data', function (Blueprint $table) {
                $table->renameColumn('ldr_value', 'ph_level');
            });
        }
    }
};
