<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div class="space-y-6">
    <section class="plant-card relative overflow-hidden rounded-[2rem] p-8 sm:p-10">
        <div class="leaf-orb is-one"></div>
        <div class="leaf-orb is-two"></div>

        <div class="relative z-10 max-w-3xl">
            <h1 class="page-title text-4xl font-semibold">Ruang kendali yang lebih tenang dan terarah.</h1>
            <p class="mt-4 max-w-2xl text-base leading-7 text-base-content/68">
                Monitoring,Mengelola Data,dan Menjaga Performa Sistem Urban Farming Tetap Stabil
            </p>
        </div>
    </section>

    <section class="grid gap-4 md:grid-cols-3">
        <div class="plant-card rounded-[1.5rem] p-5">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-base-content/45">Tampilan</p>
            <p class="mt-3 text-xl font-semibold text-primary">Modern minimalis</p>
            <p class="mt-2 text-sm text-base-content/65">Visual lebih bersih dengan aksen warna daun dan permukaan lembut.</p>
        </div>
        <div class="plant-card rounded-[1.5rem] p-5">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-base-content/45">Monitoring</p>
            <p class="mt-3 text-xl font-semibold text-primary">Realtime data</p>
            <p class="mt-2 text-sm text-base-content/65">Akses grafik dan status perangkat langsung dari menu monitoring.</p>
        </div>
        <div class="plant-card rounded-[1.5rem] p-5">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-base-content/45">Pengelolaan</p>
            <p class="mt-3 text-xl font-semibold text-primary">Navigasi ringkas</p>
            <p class="mt-2 text-sm text-base-content/65">Struktur halaman dibuat sederhana supaya fokus tetap pada data penting.</p>
        </div>
    </section>
</div>
