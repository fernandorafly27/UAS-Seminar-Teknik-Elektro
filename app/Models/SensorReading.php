<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SensorReading extends Model
{
    protected $fillable = [
        'user_id',
        'soil_humidity',
        'soil_temperature',
        'ldr_value',
        'tds_value',
        'pump_status',
        'rtc_time',
        'recorded_at',
    ];

    protected $casts = [
        'soil_humidity' => 'decimal:1',
        'soil_temperature' => 'decimal:1',
        'ldr_value' => 'integer',
        'tds_value' => 'integer',
        'pump_status' => 'boolean',
        'rtc_time' => 'datetime:H:i',
        'recorded_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
