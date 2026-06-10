<?php

namespace Tests\Feature;

use App\Models\SensorReading;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SensorReadingsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_sensor_reading_endpoint_accepts_esp_payload_aliases(): void
    {
        User::factory()->create();

        $response = $this->postJson('/api/sensor-readings', [
            'soil_moisture' => 34,
            'temperature' => 25,
            'light' => 100,
            'tds' => 0,
            'pump_status' => 'ON',
            'rtc_time' => '2026-05-28 14:45:32',
        ]);

        $response->assertCreated()
            ->assertJson(['message' => 'Data sensor tersimpan.']);

        $reading = SensorReading::firstOrFail();

        $this->assertSame('34.0', $reading->soil_humidity);
        $this->assertTrue($reading->pump_status);
        $this->assertSame(100, $reading->ldr_value);
        $this->assertSame(0, $reading->tds_value);
    }

    public function test_sensor_reading_health_endpoint_is_available(): void
    {
        $this->getJson('/api/sensor-readings/health')
            ->assertOk()
            ->assertJson(['message' => 'API sensor siap menerima data.']);
    }
}
