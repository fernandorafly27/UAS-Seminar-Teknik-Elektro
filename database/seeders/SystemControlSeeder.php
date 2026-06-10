<?php

namespace Database\Seeders;

use App\Models\SystemControl;
use App\Models\User;
use Illuminate\Database\Seeder;

class SystemControlSeeder extends Seeder
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

        SystemControl::where('user_id', $user->id)
            ->where('control_key', 'grow_light')
            ->delete();

        $controls = [
            [
                'control_name' => 'Pompa Irigasi',
                'control_key' => 'pump_irrigation',
                'description' => 'Mengatur pompa utama untuk penyiraman otomatis.',
            ],
            [
                'control_name' => 'Alarm Kelembaban',
                'control_key' => 'humidity_alert',
                'description' => 'Mengirim notifikasi ketika kelembaban berada di luar batas.',
            ],
        ];

        foreach ($controls as $control) {
            SystemControl::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'control_key' => $control['control_key'],
                ],
                [
                    'control_name' => $control['control_name'],
                    'description' => $control['description'],
                    'is_active' => $control['control_key'] !== 'humidity_alert',
                ],
            );
        }
    }
}
