<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_login_from_livewire_login_page(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@frank.test',
            'password' => Hash::make('FrankXD'),
        ]);

        Livewire::test('pages::auth.login')
            ->set('email', 'admin@frank.test')
            ->set('password', 'FrankXD')
            ->call('login')
            ->assertRedirect(route('admin'));

        $this->assertAuthenticated();
    }
}
