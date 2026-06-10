<?php

use Livewire\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts::app')] class extends Component
{
    //
};
?>

<div class="grid gap-6 lg:grid-cols-[0.85fr_1.15fr]">
    <aside class="plant-card overflow-hidden rounded-[2rem] p-6 sm:p-8">
        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-100 text-primary">
            <x-icon name="o-question-mark-circle" class="h-8 w-8" />
        </div>

        <h1 class="page-title mt-6 text-3xl font-semibold text-primary">Pusat Bantuan</h1>
        <p class="mt-3 text-sm leading-6 text-base-content/68">
            Gunakan panduan ini untuk memilih tanaman, melihat rekomendasi sensor, dan membaca status monitoring di AgroVision.
        </p>

        <div class="mt-8 space-y-3">
            <div class="rounded-[1.25rem] border border-base-content/10 bg-white/55 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-base-content/45">Status Sistem</p>
                <div class="mt-3 flex items-center gap-2 text-sm font-semibold text-primary">
                    <span class="h-2.5 w-2.5 rounded-full bg-success"></span>
                    Aktif dan siap digunakan
                </div>
            </div>

            <div class="rounded-[1.25rem] border border-base-content/10 bg-white/55 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-base-content/45">Alur Utama</p>
                <p class="mt-3 text-sm leading-6 text-base-content/68">
                    Data Tanaman → Masukkan ke Monitoring → Cek rekomendasi sensor.
                </p>
            </div>
        </div>
    </aside>

    <section class="space-y-6">
        <div class="plant-card rounded-[2rem] p-6 sm:p-8">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-lime-100 text-primary">
                    <x-icon name="o-list-bullet" class="h-6 w-6" />
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-base-content/45">Panduan Cepat</p>
                    <h2 class="text-xl font-semibold text-primary">Cara memakai rekomendasi tanaman</h2>
                </div>
            </div>

            <div class="mt-6 space-y-4">
                <div class="flex gap-4 rounded-[1.25rem] bg-white/55 p-4">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary text-sm font-bold text-primary-content">1</span>
                    <div>
                        <p class="font-semibold text-base-content">Buka Data Tanaman</p>
                        <p class="mt-1 text-sm leading-6 text-base-content/65">Lihat daftar tanaman dan nilai ideal untuk kelembapan, suhu, cahaya, nutrisi, serta durasi pompa.</p>
                    </div>
                </div>

                <div class="flex gap-4 rounded-[1.25rem] bg-white/55 p-4">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary text-sm font-bold text-primary-content">2</span>
                    <div>
                        <p class="font-semibold text-base-content">Klik Masukkan ke Monitoring</p>
                        <p class="mt-1 text-sm leading-6 text-base-content/65">Tanaman yang dipilih akan dikirim ke halaman monitoring sebagai acuan rekomendasi sensor.</p>
                    </div>
                </div>

                <div class="flex gap-4 rounded-[1.25rem] bg-white/55 p-4">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary text-sm font-bold text-primary-content">3</span>
                    <div>
                        <p class="font-semibold text-base-content">Bandingkan dengan data sensor</p>
                        <p class="mt-1 text-sm leading-6 text-base-content/65">Kartu monitoring menampilkan nilai sensor saat ini dan rekomendasi ideal tanaman terpilih.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="plant-card rounded-[2rem] p-6 sm:p-8">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-base-content/45">Informasi</p>
            <div class="mt-5 grid gap-4 md:grid-cols-2">
                <div class="rounded-[1.25rem] border border-base-content/10 bg-white/55 p-4">
                    <p class="font-semibold text-primary">Apa fungsi Monitoring?</p>
                    <p class="mt-2 text-sm leading-6 text-base-content/65">Monitoring dipakai untuk membaca kondisi sensor dan kontrol sistem seperti pompa irigasi.</p>
                </div>
                <div class="rounded-[1.25rem] border border-base-content/10 bg-white/55 p-4">
                    <p class="font-semibold text-primary">Apa fungsi Exit?</p>
                    <p class="mt-2 text-sm leading-6 text-base-content/65">Exit digunakan untuk keluar dari akun admin dan kembali ke halaman login.</p>
                </div>
            </div>
        </div>
    </section>
</div>
