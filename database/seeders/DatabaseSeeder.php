<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'program']);

        $legacyUser = User::where('email', 'admin@bems.id')->first();
        $currentUser = User::where('email', 'admin@frank.test')->first();

        if ($legacyUser && $currentUser && $legacyUser->isNot($currentUser)) {
            $currentUser->delete();
        }

        $user = $legacyUser ?? $currentUser ?? new User();

        $user->fill([
            'name' => 'Admin',
            'email' => 'admin@frank.test',
            'password' => Hash::make('FrankXD'),
        ])->save();

        $user->assignRole('admin');

        $this->call([
            SystemControlSeeder::class,
            SensorReadingSeeder::class,
            ActivityLogSeeder::class,
        ]);
    }
}
