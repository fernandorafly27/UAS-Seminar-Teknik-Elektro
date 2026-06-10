<?php

namespace Database\Seeders;

use App\Models\SensorReading;
use App\Models\User;
use Illuminate\Database\Seeder;

class SensorReadingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        if (! $user) {
            return;
        }

        if (SensorReading::where('user_id', $user->id)->exists()) {
            return;
        }

        $start = now()->subHours(23)->startOfHour();

        foreach (range(0, 23) as $hour) {
            $timestamp = $start->copy()->addHours($hour);

            SensorReading::create([
                'user_id' => $user->id,
                'soil_humidity' => fake()->numberBetween(45, 78),
                'soil_temperature' => fake()->randomFloat(1, 22, 31),
                'ldr_value' => fake()->numberBetween(35, 95),
                'tds_value' => fake()->numberBetween(500, 820),
                'pump_status' => fake()->boolean(25),
                'rtc_time' => $timestamp,
                'recorded_at' => $timestamp,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
        }
    }
}
