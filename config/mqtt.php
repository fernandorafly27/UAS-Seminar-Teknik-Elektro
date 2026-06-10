<?php

return [
    'host' => env('MQTT_HOST', '127.0.0.1'),
    'port' => (int) env('MQTT_PORT', 1883),
    'client_id' => env('MQTT_CLIENT_ID', 'agrovision-laravel-subscriber'),
    'username' => env('MQTT_USERNAME'),
    'password' => env('MQTT_PASSWORD'),
    'sensor_topic' => env('MQTT_SENSOR_TOPIC', 'agrovision/sensors'),
    'qos' => (int) env('MQTT_QOS', 0),
];
