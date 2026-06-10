<?php

namespace App\Services;

use App\Models\SensorReading;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SensorReadingIngestor
{
    /**
     * @throws ValidationException
     */
    public function ingest(array $payload): SensorReading
    {
        $data = $this->normalize($payload);

        $data = Validator::make($data, [
            'soil_humidity' => ['required', 'numeric', 'min:0', 'max:100'],
            'soil_temperature' => ['required', 'numeric', 'min:0', 'max:80'],
            'ldr_value' => ['required', 'integer', 'min:0', 'max:100'],
            'tds_value' => ['required', 'integer', 'min:0', 'max:5000'],
            'pump_status' => ['required', 'boolean'],
            'rtc_time' => ['nullable', 'date'],
            'recorded_at' => ['nullable', 'date'],
        ])->validate();

        $user = User::first();

        if (! $user) {
            throw ValidationException::withMessages([
                'user' => 'User belum ada. Jalankan seeder atau buat akun admin dulu.',
            ]);
        }

        return SensorReading::create([
            'user_id' => $user->id,
            ...$data,
            'recorded_at' => $data['recorded_at'] ?? now(),
        ]);
    }

    public function normalize(array $payload): array
    {
        $pumpStatus = $payload['pump_status'] ?? $payload['pump'] ?? null;

        if (is_string($pumpStatus)) {
            $pumpStatus = match (strtolower($pumpStatus)) {
                'on', 'nyala', 'hidup', 'true', '1' => true,
                'off', 'mati', 'false', '0' => false,
                default => $pumpStatus,
            };
        }

        return [
            ...$payload,
            'soil_humidity' => $payload['soil_humidity'] ?? $payload['soil_moisture'] ?? null,
            'soil_temperature' => $payload['soil_temperature'] ?? $payload['temperature'] ?? null,
            'ldr_value' => $payload['ldr_value'] ?? $payload['light'] ?? null,
            'tds_value' => $payload['tds_value'] ?? $payload['tds'] ?? null,
            'pump_status' => $pumpStatus,
            'recorded_at' => $payload['recorded_at'] ?? $payload['rtc_time'] ?? null,
        ];
    }
}
