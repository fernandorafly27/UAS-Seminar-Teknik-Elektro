<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivityLogSeeder extends Seeder
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

        if (ActivityLog::where('user_id', $user->id)->exists()) {
            return;
        }

        $logs = [
            ['type' => 'DATA', 'message' => 'Data sensor diterima dari node greenhouse', 'icon' => 'o-cloud-arrow-up'],
            ['type' => 'ALERT', 'message' => 'Kelembaban tanah turun di bawah ambang aman', 'icon' => 'o-exclamation-triangle'],
            ['type' => 'PUMP', 'message' => 'Pompa irigasi otomatis menyala selama 3 menit', 'icon' => 'o-power'],
            ['type' => 'CONTROL', 'message' => 'Mode irigasi manual diaktifkan dari dashboard', 'icon' => 'o-cog-6-tooth'],
            ['type' => 'DATA', 'message' => 'Sinkronisasi RTC DS3231 berhasil', 'icon' => 'o-clock'],
        ];

        foreach ($logs as $index => $log) {
            ActivityLog::create([
                'user_id' => $user->id,
                'type' => $log['type'],
                'message' => $log['message'],
                'icon' => $log['icon'],
                'occurred_at' => now()->subMinutes($index * 18),
            ]);
        }
    }
}
