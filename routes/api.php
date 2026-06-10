<?php

use App\Services\SensorReadingIngestor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/sensor-readings/health', function () {
    return response()->json([
        'message' => 'API sensor siap menerima data.',
        'server_time' => now()->toDateTimeString(),
    ]);
});

Route::get('/plant-config/{plant?}', function (?string $plant = null) {
    $plants = config('plants');
    $plant = $plant ?: 'Cabe';

    if (! array_key_exists($plant, $plants)) {
        return response()->json([
            'message' => 'Tanaman tidak ditemukan.',
            'available_plants' => array_keys($plants),
        ], 404);
    }

    return response()->json($plants[$plant]);
});

Route::post('/sensor-readings', function (Request $request, SensorReadingIngestor $ingestor) {
    $reading = $ingestor->ingest($request->all());

    return response()->json([
        'message' => 'Data sensor tersimpan.',
        'id' => $reading->id,
    ], 201);
});
