# MQTT Mosquitto Sensor Ingestion

Struktur ini menyiapkan jalur alternatif selain HTTP:

```text
ESP32 -> MQTT Mosquitto -> Laravel mqtt:subscribe-sensors -> sensor_readings -> dashboard polling
```

## Broker

Service Mosquitto tersedia di `docker-compose.yml`:

```bash
docker compose up -d mosquitto
```

Port broker:

```text
1883
```

Config broker:

```text
.docker/mosquitto/mosquitto.conf
```

## Laravel Subscriber

Subscriber disiapkan sebagai artisan command:

```bash
php artisan mqtt:subscribe-sensors
```

Untuk test satu pesan saja:

```bash
php artisan mqtt:subscribe-sensors --once
```

Command ini membaca config dari `config/mqtt.php`.

Tambahkan ke `.env`:

```env
MQTT_HOST=127.0.0.1
MQTT_PORT=1883
MQTT_CLIENT_ID=agrovision-laravel-subscriber
MQTT_USERNAME=null
MQTT_PASSWORD=null
MQTT_SENSOR_TOPIC=agrovision/sensors
MQTT_QOS=0
```

Jika Laravel berjalan di container Docker yang satu network dengan Mosquitto, gunakan:

```env
MQTT_HOST=mosquitto
```

Jika Laravel berjalan langsung di laptop, gunakan:

```env
MQTT_HOST=127.0.0.1
```

## Dependency

Subscriber membutuhkan package:

```bash
composer require php-mqtt/client
```

Di sesi ini dependency sudah tercatat di `composer.json` dan `composer.lock`, tetapi instalasi ke `vendor/` gagal karena folder `vendor` dimiliki user `nobody`. Jalankan ulang perintah Composer dari environment yang punya izin tulis ke folder `vendor`.

## Topic

Default topic:

```text
agrovision/sensors
```

## Payload ESP32

Kirim JSON object seperti ini:

```json
{
  "soil_humidity": 34,
  "soil_temperature": 25,
  "ldr_value": 100,
  "tds_value": 0,
  "pump_status": true,
  "rtc_time": "2026-05-28 15:22:58",
  "recorded_at": "2026-05-28 15:22:58"
}
```

Alias yang tetap diterima:

```json
{
  "soil_moisture": 34,
  "temperature": 25,
  "light": 100,
  "tds": 0,
  "pump": "ON",
  "rtc_time": "2026-05-28 15:22:58"
}
```

## Test Publish

Jika punya `mosquitto_pub`:

```bash
mosquitto_pub -h 127.0.0.1 -p 1883 -t agrovision/sensors -m '{"soil_humidity":34,"soil_temperature":25,"ldr_value":100,"tds_value":0,"pump_status":true}'
```

Dashboard tetap memakai Livewire polling 3 detik, jadi data MQTT yang sudah masuk database akan tampil otomatis.
