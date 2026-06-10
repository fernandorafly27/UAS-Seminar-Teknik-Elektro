<?php

namespace App\Console\Commands;

use App\Services\SensorReadingIngestor;
use Illuminate\Console\Command;
use Illuminate\Validation\ValidationException;
use JsonException;

class MqttSensorSubscribe extends Command
{
    protected $signature = 'mqtt:subscribe-sensors {--once : Stop after the first MQTT message is processed}';

    protected $description = 'Subscribe to MQTT sensor payloads and store them as sensor readings.';

    public function handle(SensorReadingIngestor $ingestor): int
    {
        if (! class_exists(\PhpMqtt\Client\MqttClient::class)) {
            $this->error('Package MQTT belum terpasang.');
            $this->line('Jalankan: composer require php-mqtt/client');

            return self::FAILURE;
        }

        $host = config('mqtt.host');
        $port = config('mqtt.port');
        $topic = config('mqtt.sensor_topic');
        $clientId = config('mqtt.client_id').'-'.getmypid();
        $qos = config('mqtt.qos');

        $settings = (new \PhpMqtt\Client\ConnectionSettings)
            ->setUsername(config('mqtt.username'))
            ->setPassword(config('mqtt.password'));

        $mqtt = new \PhpMqtt\Client\MqttClient($host, $port, $clientId);

        $this->info("Menghubungkan ke MQTT broker {$host}:{$port}...");
        $mqtt->connect($settings, true);

        $this->info("Subscribe topic: {$topic}");

        $mqtt->subscribe($topic, function (string $topic, string $message) use ($ingestor, $mqtt): void {
            $this->line("Pesan MQTT diterima dari {$topic}: {$message}");

            try {
                $payload = json_decode($message, true, 512, JSON_THROW_ON_ERROR);

                if (! is_array($payload)) {
                    $this->warn('Payload MQTT harus berupa JSON object.');

                    return;
                }

                $reading = $ingestor->ingest($payload);

                $this->info("Data sensor tersimpan. ID: {$reading->id}");

                if ($this->option('once')) {
                    $mqtt->interrupt();
                }
            } catch (JsonException $exception) {
                $this->warn('Payload MQTT bukan JSON valid: '.$exception->getMessage());
            } catch (ValidationException $exception) {
                $this->warn('Payload MQTT tidak valid: '.json_encode($exception->errors()));
            }
        }, $qos);

        $mqtt->loop(true);
        $mqtt->disconnect();

        return self::SUCCESS;
    }
}
