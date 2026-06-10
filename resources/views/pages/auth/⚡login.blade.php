<?php

use Livewire\Component;

use Illuminate\Support\Facades\Auth;
use Mary\Traits\Toast;
use Livewire\Attributes\Layout;

new #[Layout('layouts::guest')] class extends Component
{
    use Toast;
    public $email;
    public $password;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|min:6',
    ];
    public function login()
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            session()->regenerate();

            return redirect()->route('admin');
        }

        $this->addError('email', 'Email atau password salah.');
        $this->error('Email atau password salah', position: 'toast-top toast-center');
    }
};
?>

<div class="auth-page flex items-center px-4 py-8 sm:px-6 lg:px-10">
    <div class="mx-auto grid w-full max-w-6xl gap-6 lg:grid-cols-[1.1fr_0.9fr]">
        <section class="auth-copy plant-card rounded-[2rem] p-8 sm:p-10 lg:p-12">
            <span class="plant-badge inline-flex w-fit items-center gap-2 rounded-full px-4 py-2 text-xs font-semibold uppercase tracking-[0.28em]">
                <span class="h-2 w-2 rounded-full bg-success"></span>
                Smart Plant Monitoring
            </span>

            <div class="relative z-10 mt-8 max-w-xl">
                <p class="text-sm font-medium uppercase tracking-[0.24em] text-primary/70">Sistem budidaya yang lebih tenang</p>
                <h1 class="page-title mt-4 text-4xl font-semibold leading-tight sm:text-5xl">
                    Dashboard modern dengan nuansa tumbuhan yang bersih dan fokus.
                </h1>
                <p class="mt-5 max-w-lg text-base leading-7 text-base-content/70">
                    Pantau kelembaban, suhu, cahaya, nutrisi, dan status pompa dalam satu ruang kerja yang ringan, rapi, dan mudah dibaca.
                </p>
            </div>

            <div class="relative z-10 mt-10 grid gap-4 sm:grid-cols-3">
                <div class="metric-card plant-card rounded-[1.5rem] p-4">
                    <p class="text-xs uppercase tracking-[0.24em] text-base-content/45">Realtime</p>
                    <p class="mt-3 text-2xl font-semibold text-primary">24/7</p>
                    <p class="mt-2 text-sm text-base-content/65">Pemantauan berkelanjutan untuk media tanam.</p>
                </div>
                <div class="metric-card plant-card rounded-[1.5rem] p-4">
                    <p class="text-xs uppercase tracking-[0.24em] text-base-content/45">Sensor</p>
                    <p class="mt-3 text-2xl font-semibold text-primary">5 titik</p>
                    <p class="mt-2 text-sm text-base-content/65">Data inti tanaman tersaji jelas tanpa visual berlebihan.</p>
                </div>
                <div class="metric-card plant-card rounded-[1.5rem] p-4">
                    <p class="text-xs uppercase tracking-[0.24em] text-base-content/45">Kontrol</p>
                    <p class="mt-3 text-2xl font-semibold text-primary">Manual</p>
                    <p class="mt-2 text-sm text-base-content/65">Aktivasi pompa dan sistem langsung dari panel.</p>
                </div>
            </div>
        </section>

        <section class="auth-panel plant-card relative overflow-hidden rounded-[2rem] p-6 sm:p-8">
            <div class="leaf-orb is-one"></div>
            <div class="leaf-orb is-two"></div>

            <div class="relative z-10">
                <div class="status-pill inline-flex items-center gap-2 rounded-full px-4 py-2 text-xs font-semibold uppercase tracking-[0.26em] text-primary">
                    <span class="h-2 w-2 rounded-full bg-success"></span>
                    Secure Access
                </div>

                <h2 class="page-title mt-6 text-3xl font-semibold">Masuk ke AgroVision</h2>
                <p class="mt-3 text-sm leading-6 text-base-content/68">
                    Gunakan akun Anda untuk membuka dashboard monitoring tanaman.
                </p>

                <form wire:submit="login" class="mt-8 space-y-5">
                    <x-input
                        label="Email"
                        wire:model="email"
                        placeholder="admin@frank.test"
                        icon="o-user"
                        hint="Masukkan email akun"
                        class="bg-white/70"
                    />

                    <x-password
                        wire:model="password"
                        wire:keydown.enter="login"
                        label="Password"
                        icon="o-key"
                        hint="Minimal 6 karakter"
                        right
                        class="bg-white/70"
                    />

                    <div class="flex items-center justify-between gap-4 rounded-[1.25rem] border soft-divider bg-white/55 px-4 py-3 text-sm text-base-content/65">
                        <span>Akses halaman admin dan monitoring sensor.</span>
                        <span class="font-semibold text-primary">Live</span>
                    </div>

                    <x-button class="btn btn-primary h-13 w-full text-base font-semibold" label="Masuk ke Dashboard" type="submit" />
                </form>
            </div>
        </section>
    </div>
</div>
