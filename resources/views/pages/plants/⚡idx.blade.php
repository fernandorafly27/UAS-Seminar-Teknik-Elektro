<?php

use Livewire\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts::app')] class extends Component
{
    public array $plants = [];

    public function mount(): void
    {
        $this->plants = array_values(config('plants'));
    }
};
?>

<div class="space-y-6">
    <section class="plant-card overflow-hidden rounded-[2rem] p-6 sm:p-8">
        <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
            <div>
                <span class="plant-badge inline-flex rounded-full px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.28em]">
                    Data Tanaman
                </span>
                <h1 class="page-title mt-4 text-3xl font-semibold text-primary">Tanaman yang Akan Ditanam</h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-base-content/68">
                    Referensi nilai ideal untuk kelembapan, suhu, cahaya, nutrisi, dan durasi pompa awal.
                </p>
            </div>
        </div>
    </section>

    <section class="plant-card overflow-hidden rounded-[2rem]">
        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead>
                    <tr class="border-b border-base-content/10 text-sm text-primary">
                        <th class="px-5 py-4 font-semibold">Tanaman</th>
                        <th class="px-5 py-4 font-semibold">Kelembapan tanah ideal</th>
                        <th class="px-5 py-4 font-semibold">Suhu tanah / akar ideal</th>
                        <th class="px-5 py-4 font-semibold">Intensitas cahaya</th>
                        <th class="px-5 py-4 font-semibold">TDS / nutrisi</th>
                        <th class="px-5 py-4 font-semibold">Durasi pompa awal</th>
                        <th class="px-5 py-4 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($plants as $plant)
                        <tr class="border-b border-base-content/10 last:border-b-0">
                            <td class="px-5 py-4 font-semibold text-base-content">{{ $plant['name'] }}</td>
                            <td class="px-5 py-4">{{ $plant['humidity'] }}</td>
                            <td class="px-5 py-4">{{ $plant['temperature'] }}</td>
                            <td class="px-5 py-4">{{ $plant['light'] }}</td>
                            <td class="px-5 py-4">{{ $plant['tds'] }}</td>
                            <td class="px-5 py-4">{{ $plant['pump'] }}</td>
                            <td class="px-5 py-4">
                                <x-button
                                    label="Masukkan ke Monitoring"
                                    icon="o-arrow-right"
                                    link="{{ route('monitoring', ['plant' => $plant['slug']]) }}"
                                    class="btn-primary btn-sm whitespace-nowrap"
                                />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</div>
