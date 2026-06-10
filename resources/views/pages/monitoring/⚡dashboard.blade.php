<?php

use Livewire\Component;
use App\Models\SensorReading;
use App\Models\SystemControl;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

new #[Layout('layouts::app')] class extends Component
{
    public $latestReading;
    public $systemControls;
    public $activityLogs;
    public $chartData;
    public $humidityChart;
    public $chartStats;
    public $isChartPreview = false;
    public $sensorBands;
    public $pumpDurationData;
    public ?string $lastUpdatedText = null;
    public bool $isNodeOnline = false;
    public string $selectedPlant = 'Cabe';
    public array $plantRecommendations = [];
    public array $selectedRecommendation = [];

    public function mount()
    {
        $this->plantRecommendations = $this->plantRecommendations();
        $requestedPlant = (string) request()->query('plant', 'Cabe');
        $this->selectedPlant = array_key_exists($requestedPlant, $this->plantRecommendations)
            ? $requestedPlant
            : 'Cabe';
        $this->selectedRecommendation = $this->plantRecommendations[$this->selectedPlant];

        $this->loadData();
    }

    public function loadData()
    {
        $userId = Auth::id();

        if (! $userId) {
            $this->latestReading = null;
            $this->systemControls = collect();
            $this->activityLogs = collect();
            $this->chartData = $this->sampleChartData();
            $this->humidityChart = $this->buildHumidityChart($this->chartData);
            $this->chartStats = $this->buildChartStats($this->chartData);
            $this->isChartPreview = true;
            $this->sensorBands = [];
            $this->pumpDurationData = [];
            $this->lastUpdatedText = null;
            $this->isNodeOnline = false;

            return;
        }

        // Get latest sensor reading
        $this->latestReading = SensorReading::where('user_id', $userId)
            ->latest('created_at')
            ->first();

        // Get system controls
        $this->systemControls = SystemControl::where('user_id', $userId)
            ->where('control_key', '!=', 'grow_light')
            ->get();

        // Get activity logs (last 10)
        $this->activityLogs = ActivityLog::where('user_id', $userId)
            ->latest('occurred_at')
            ->take(10)
            ->get();

        // Get last 24 hours data for chart
        $readings = SensorReading::where('user_id', $userId)
            ->where('created_at', '>=', now()->subHours(24))
            ->orderBy('created_at')
            ->get();

        $this->chartData = $readings->map(function ($reading) {
            return [
                'time' => $reading->created_at->format('H:i'),
                'humidity' => (float) $reading->soil_humidity,
                'temperature' => (float) $reading->soil_temperature,
                'light' => (int) $reading->ldr_value,
                'tds' => (int) $reading->tds_value,
                'pump_status' => (bool) $reading->pump_status,
            ];
        })->toArray();

        $this->isChartPreview = $this->chartData === [];

        if ($this->isChartPreview) {
            $this->chartData = $this->sampleChartData();
        }

        $this->humidityChart = $this->buildHumidityChart($this->chartData);
        $this->chartStats = $this->buildChartStats($this->chartData);
        $this->sensorBands = $this->buildSensorBands($this->latestReading);
        $this->lastUpdatedText = $this->latestReading?->created_at?->format('H:i:s');
        $this->isNodeOnline = $this->latestReading
            && $this->latestReading->created_at->greaterThanOrEqualTo(now()->subSeconds(20));

        // Calculate pump duration (in hours)
        $this->pumpDurationData = [
            ['day' => 'Sen', 'duration' => 14],
            ['day' => 'Sel', 'duration' => 22],
            ['day' => 'Rab', 'duration' => 18],
            ['day' => 'Kam', 'duration' => 30],
            ['day' => 'Jum', 'duration' => 15],
            ['day' => 'Sab', 'duration' => 25],
            ['day' => 'Min', 'duration' => 19],
        ];
    }

    public function toggleControl($controlId)
    {
        $control = SystemControl::where('user_id', Auth::id())->find($controlId);

        if ($control) {
            $control->is_active = !$control->is_active;
            $control->last_triggered_at = now();
            $control->save();

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'type' => 'CONTROL',
                'message' => $control->is_active
                    ? "{$control->control_name} diaktifkan"
                    : "{$control->control_name} dinonaktifkan",
                'icon' => $control->is_active ? 'o-check' : 'o-x-mark',
                'occurred_at' => now(),
            ]);

            $this->loadData();
        }
    }

    public function render()
    {
        return $this->view([
            'latestReading' => $this->latestReading,
            'systemControls' => $this->systemControls,
            'activityLogs' => $this->activityLogs,
            'chartData' => $this->chartData,
            'humidityChart' => $this->humidityChart,
            'chartStats' => $this->chartStats,
            'isChartPreview' => $this->isChartPreview,
            'sensorBands' => $this->sensorBands,
            'pumpDurationData' => $this->pumpDurationData,
            'lastUpdatedText' => $this->lastUpdatedText,
            'isNodeOnline' => $this->isNodeOnline,
            'selectedPlant' => $this->selectedPlant,
            'selectedRecommendation' => $this->selectedRecommendation,
        ]);
    }

    private function plantRecommendations(): array
    {
        return config('plants');
    }

    private function sampleChartData(): array
    {
        return [
            ['time' => '00:00', 'humidity' => 48, 'temperature' => 24.1, 'light' => 18, 'tds' => 610, 'pump_status' => false],
            ['time' => '03:00', 'humidity' => 52, 'temperature' => 24.8, 'light' => 12, 'tds' => 635, 'pump_status' => false],
            ['time' => '06:00', 'humidity' => 61, 'temperature' => 25.9, 'light' => 42, 'tds' => 660, 'pump_status' => true],
            ['time' => '09:00', 'humidity' => 57, 'temperature' => 27.2, 'light' => 68, 'tds' => 685, 'pump_status' => false],
            ['time' => '12:00', 'humidity' => 69, 'temperature' => 29.0, 'light' => 86, 'tds' => 710, 'pump_status' => true],
            ['time' => '15:00', 'humidity' => 64, 'temperature' => 28.4, 'light' => 78, 'tds' => 695, 'pump_status' => false],
            ['time' => '18:00', 'humidity' => 72, 'temperature' => 26.7, 'light' => 46, 'tds' => 675, 'pump_status' => true],
            ['time' => '21:00', 'humidity' => 66, 'temperature' => 25.3, 'light' => 24, 'tds' => 650, 'pump_status' => false],
        ];
    }

    private function buildHumidityChart(array $readings): array
    {
        $count = count($readings);

        if ($count === 0) {
            return ['line' => '', 'area' => '', 'labels' => []];
        }

        $points = collect($readings)->values()->map(function ($reading, $index) use ($count) {
            $x = $count === 1 ? 50 : ($index / ($count - 1)) * 100;
            $humidity = max(0, min(100, (float) $reading['humidity']));
            $y = 44 - (($humidity / 100) * 44);

            return round($x, 2).','.round($y, 2);
        })->implode(' ');

        $labels = collect($readings)
            ->when($count > 8, fn ($items) => $items->filter(fn ($reading, $index) => $index % 4 === 0 || $index === $count - 1))
            ->map(fn ($reading) => $reading['time'])
            ->values()
            ->toArray();

        return [
            'line' => $points,
            'area' => "0,44 {$points} 100,44",
            'labels' => $labels,
        ];
    }

    private function buildChartStats(array $readings): array
    {
        if ($readings === []) {
            return [
                'avg_humidity' => null,
                'min_humidity' => null,
                'max_humidity' => null,
                'pump_active_count' => 0,
            ];
        }

        $humidityValues = collect($readings)->pluck('humidity')->map(fn ($value) => (float) $value);

        return [
            'avg_humidity' => round($humidityValues->avg(), 1),
            'min_humidity' => round($humidityValues->min(), 1),
            'max_humidity' => round($humidityValues->max(), 1),
            'pump_active_count' => collect($readings)->where('pump_status', true)->count(),
        ];
    }

    private function buildSensorBands(?SensorReading $reading): array
    {
        if (! $reading) {
            return [];
        }

        return [
            [
                'label' => 'Suhu',
                'value' => number_format((float) $reading->soil_temperature, 1).'°C',
                'percent' => max(0, min(100, (((float) $reading->soil_temperature - 15) / 25) * 100)),
                'color' => 'from-orange-400 to-amber-400',
            ],
            [
                'label' => 'Cahaya',
                'value' => (int) $reading->ldr_value.'%',
                'percent' => max(0, min(100, (int) $reading->ldr_value)),
                'color' => 'from-yellow-400 to-lime-400',
            ],
            [
                'label' => 'TDS',
                'value' => (int) $reading->tds_value.'ppm',
                'percent' => max(0, min(100, ((int) $reading->tds_value / 1000) * 100)),
                'color' => 'from-emerald-400 to-lime-400',
            ],
        ];
    }
};
?>

<style>
    .monitoring-dashboard {
        color: var(--color-base-content);
    }

    .monitoring-dashboard .text-base-content,
    .monitoring-dashboard .text-gray-800,
    .monitoring-dashboard .text-gray-700,
    .monitoring-dashboard .text-gray-600 {
        color: var(--color-base-content) !important;
    }

    .monitoring-dashboard .text-base-content\/70,
    .monitoring-dashboard .text-gray-500 {
        color: color-mix(in oklab, var(--color-base-content) 70%, transparent) !important;
    }

    .monitoring-dashboard .text-base-content\/60,
    .monitoring-dashboard .text-gray-400 {
        color: color-mix(in oklab, var(--color-base-content) 60%, transparent) !important;
    }

    .monitoring-dashboard .text-base-content\/50 {
        color: color-mix(in oklab, var(--color-base-content) 50%, transparent) !important;
    }

    .monitoring-dashboard .bg-base-200\/60,
    .monitoring-dashboard .bg-gray-50 {
        background-color: color-mix(in oklab, var(--color-base-200) 60%, transparent) !important;
    }

    .monitoring-dashboard .border-base-content\/20,
    .monitoring-dashboard .border-b {
        border-color: color-mix(in oklab, var(--color-base-content) 20%, transparent) !important;
    }

    .monitoring-dashboard .sensor-chart {
        background:
            linear-gradient(to right, color-mix(in oklab, var(--color-base-content) 8%, transparent) 1px, transparent 1px),
            linear-gradient(to bottom, color-mix(in oklab, var(--color-base-content) 8%, transparent) 1px, transparent 1px),
            linear-gradient(135deg, color-mix(in oklab, var(--color-primary) 12%, transparent), color-mix(in oklab, var(--color-success) 10%, transparent));
        background-size: 20% 100%, 100% 25%, 100% 100%;
    }

    .monitoring-dashboard .chart-line {
        filter: drop-shadow(0 8px 18px color-mix(in oklab, var(--color-info) 35%, transparent));
    }

    .monitoring-dashboard .chart-layout {
        display: grid;
        gap: 1rem;
    }

    .monitoring-dashboard .chart-plot {
        position: absolute;
        inset: 5.5rem 1rem 2.5rem;
    }

    .monitoring-dashboard .chart-plot svg {
        width: 100%;
        height: 100%;
        overflow: visible;
    }

    @media (min-width: 1024px) {
        .monitoring-dashboard .chart-layout {
            grid-template-columns: minmax(0, 1fr) 180px;
        }
    }

</style>

<div class="monitoring-dashboard" wire:poll.3s="loadData">
    <!-- Header -->
    <x-card class="plant-card mb-6 overflow-hidden border-0" shadow separator>
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <span class="plant-badge inline-flex rounded-full px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.28em]">Monitoring Tanaman</span>
                <x-slot:title class="page-title mt-4 text-3xl font-semibold">Dashboard Monitoring</x-slot:title>
                <p class="mt-2 text-sm text-base-content/70">Rekomendasi sensor untuk tanaman {{ $selectedPlant }}</p>
            </div>
            <div class="status-pill w-fit rounded-full px-4 py-3 text-sm md:text-right">
                <div class="flex items-center gap-2 font-semibold {{ $isNodeOnline ? 'text-success' : 'text-warning' }} md:justify-end">
                    <span class="h-2.5 w-2.5 rounded-full {{ $isNodeOnline ? 'bg-success' : 'bg-warning' }}"></span>
                    {{ $isNodeOnline ? 'ESP32 online' : 'Menunggu data ESP32' }}
                </div>
                <p class="mt-1 text-xs text-base-content/60">
                    Update terakhir: {{ $lastUpdatedText ?? '--:--:--' }}
                </p>
            </div>
        </div>
    </x-card>

    <!-- Sensor Readings Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
        <!-- Kelembaban Tanah -->
        <x-card class="plant-card metric-card border-0" shadow>
            <div class="text-center">
                <div class="mb-2 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-cyan-100/80">
                    <x-icon name="o-cloud" class="w-6 h-6 text-cyan-700" />
                </div>
                <p class="text-xs text-base-content/70 font-semibold">KELEMBABAN TANAH</p>
                @if($latestReading)
                    <p class="text-2xl font-bold text-cyan-400">{{ number_format((float) $latestReading->soil_humidity, 1) }}%</p>
                    <p class="text-xs text-green-500 mt-1">Rekom: {{ $selectedRecommendation['humidity'] }}</p>
                @else
                    <p class="text-2xl font-bold text-base-content/50">--</p>
                    <p class="text-xs text-green-500 mt-1">Rekom: {{ $selectedRecommendation['humidity'] }}</p>
                @endif
            </div>
        </x-card>

        <!-- Intensitas Cahaya -->
        <x-card class="plant-card metric-card border-0" shadow>
            <div class="text-center">
                <div class="mb-2 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-yellow-100/90">
                    <x-icon name="o-light-bulb" class="w-6 h-6 text-yellow-600" />
                </div>
                <p class="text-xs text-base-content/70 font-semibold">INTENSITAS CAHAYA</p>
                @if($latestReading)
                    <p class="text-2xl font-bold text-yellow-500">{{ (int) $latestReading->ldr_value }}%</p>
                    <p class="text-xs text-green-500 mt-1">Rekom: {{ $selectedRecommendation['light'] }}</p>
                @else
                    <p class="text-2xl font-bold text-base-content/50">--</p>
                    <p class="text-xs text-green-500 mt-1">Rekom: {{ $selectedRecommendation['light'] }}</p>
                @endif
            </div>
        </x-card>

        <!-- TDS / Nutrisi -->
        <x-card class="plant-card metric-card border-0" shadow>
            <div class="text-center">
                <div class="mb-2 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100/90">
                    <x-icon name="o-circle-stack" class="w-6 h-6 text-green-600" />
                </div>
                <p class="text-xs text-base-content/70 font-semibold">TDS / NUTRISI</p>
                @if($latestReading)
                    <p class="text-2xl font-bold text-green-400">{{ $latestReading->tds_value ?? '-' }}ppm</p>
                    <p class="text-xs text-green-500 mt-1">Rekom: {{ $selectedRecommendation['tds'] }}</p>
                @else
                    <p class="text-2xl font-bold text-base-content/50">--</p>
                    <p class="text-xs text-green-500 mt-1">Rekom: {{ $selectedRecommendation['tds'] }}</p>
                @endif
            </div>
        </x-card>

        <!-- Pompa -->
        <x-card class="plant-card metric-card border-0" shadow>
            <div class="text-center">
                <div class="mb-2 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-100/90">
                    <x-icon name="o-power" class="w-6 h-6 text-red-600" />
                </div>
                <p class="text-xs text-base-content/70 font-semibold">POMPA 12V</p>
                @if($latestReading)
                    <p class="text-2xl font-bold {{ $latestReading->pump_status ? 'text-green-400' : 'text-red-400' }}">
                        {{ $latestReading->pump_status ? 'ON' : 'OFF' }}
                    </p>
                    <p class="text-xs {{ $latestReading->pump_status ? 'text-green-500' : 'text-red-500' }} mt-1">
                        Rekom: {{ $selectedRecommendation['pump'] }}
                    </p>
                @else
                    <p class="text-2xl font-bold text-base-content/50">--</p>
                    <p class="text-xs text-green-500 mt-1">Rekom: {{ $selectedRecommendation['pump'] }}</p>
                @endif
            </div>
        </x-card>

        <!-- RTC -->
        <x-card class="plant-card metric-card border-0" shadow>
            <div class="text-center">
                <div class="mb-2 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-lime-100/90">
                    <x-icon name="o-clock" class="w-6 h-6 text-lime-700" />
                </div>
                <p class="text-xs text-base-content/70 font-semibold">RTC DS3231</p>
                @if($latestReading)
                    <p class="text-lg font-bold text-lime-700">{{ $latestReading->rtc_time?->format('H:i') ?? '--:--' }}</p>
                    <p class="text-xs text-blue-500 mt-1">Sinkron</p>
                @else
                    <p class="text-2xl font-bold text-base-content/50">--:--</p>
                @endif
            </div>
        </x-card>
    </div>

    <!-- Charts and Controls -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Chart: Kelembaban Tanah -->
        <x-card class="plant-card lg:col-span-2 border-0" shadow separator>
            <x-slot:title>
                <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                    <span>Kelembaban Tanah - 24 Jam Terakhir</span>
                    @unless($isChartPreview)
                        <span class="text-xs font-medium text-base-content/60">{{ count($chartData) }} pembacaan</span>
                    @endunless
                </div>
            </x-slot:title>

            <div class="chart-layout">
                <div class="sensor-chart relative h-72 overflow-hidden rounded-[1.5rem] border border-base-content/10 p-4">
                    @if($humidityChart['line'])
                        <div class="absolute left-4 top-5 z-10">
                            <p class="text-xs font-semibold text-base-content/60">Rata-rata</p>
                            <p class="text-4xl font-bold text-cyan-400">{{ $chartStats['avg_humidity'] }}%</p>
                        </div>

                        <div class="chart-plot">
                            <svg viewBox="0 0 100 44" preserveAspectRatio="none" role="img" aria-label="Grafik kelembaban tanah 24 jam">
                                <defs>
                                    <linearGradient id="humidityArea" x1="0" x2="0" y1="0" y2="1">
                                        <stop offset="0%" stop-color="#22d3ee" stop-opacity="0.42" />
                                        <stop offset="100%" stop-color="#22c55e" stop-opacity="0.04" />
                                    </linearGradient>
                                    <linearGradient id="humidityLine" x1="0" x2="1" y1="0" y2="0">
                                        <stop offset="0%" stop-color="#06b6d4" />
                                        <stop offset="55%" stop-color="#22d3ee" />
                                        <stop offset="100%" stop-color="#22c55e" />
                                    </linearGradient>
                                </defs>
                                <polygon points="{{ $humidityChart['area'] }}" fill="url(#humidityArea)" />
                                <polyline class="chart-line" points="{{ $humidityChart['line'] }}" fill="none" stroke="url(#humidityLine)" stroke-width="2.7" stroke-linecap="round" stroke-linejoin="round" vector-effect="non-scaling-stroke" />
                            </svg>
                        </div>

                        <div class="absolute inset-x-4 bottom-3 flex justify-between gap-2 text-[11px] font-medium text-base-content/50">
                            @foreach($humidityChart['labels'] as $label)
                                <span class="min-w-0">{{ $label }}</span>
                            @endforeach
                        </div>
                    @else
                        <div class="flex h-full items-center justify-center">
                            <p class="text-sm text-base-content/60">Belum ada data grafik</p>
                        </div>
                    @endif
                </div>

                <div class="grid gap-3 sm:grid-cols-3 lg:grid-cols-1">
                    <div class="rounded-[1.25rem] border border-base-content/10 bg-base-200/60 p-3">
                        <p class="text-xs font-semibold text-base-content/60">Minimum</p>
                        <p class="text-2xl font-bold text-blue-400">{{ $chartStats['min_humidity'] ?? '--' }}%</p>
                    </div>
                    <div class="rounded-[1.25rem] border border-base-content/10 bg-base-200/60 p-3">
                        <p class="text-xs font-semibold text-base-content/60">Maksimum</p>
                        <p class="text-2xl font-bold text-success">{{ $chartStats['max_humidity'] ?? '--' }}%</p>
                    </div>
                    <div class="rounded-[1.25rem] border border-base-content/10 bg-base-200/60 p-3">
                        <p class="text-xs font-semibold text-base-content/60">Pompa Aktif</p>
                        <p class="text-2xl font-bold text-red-400">{{ $chartStats['pump_active_count'] ?? 0 }}x</p>
                    </div>
                </div>
            </div>
        </x-card>

        <!-- System Controls -->
        <x-card class="plant-card border-0" shadow separator>
            <x-slot:title>Kontrol Sistem</x-slot:title>
            <div class="space-y-4">
                @forelse($systemControls as $control)
                    <div class="flex items-center justify-between rounded-[1.25rem] bg-base-200/60 p-3">
                        <div class="flex items-center gap-2">
                            <x-icon name="o-cog-6-tooth" class="w-4 h-4 text-base-content/70" />
                            <span class="text-sm font-medium">{{ $control->control_name }}</span>
                        </div>
                        <input
                            type="checkbox"
                            wire:click="toggleControl({{ $control->id }})"
                            @checked($control->is_active)
                            class="toggle toggle-sm toggle-success"
                        />
                    </div>
                @empty
                    <p class="text-sm text-base-content/60 text-center py-4">Tidak ada kontrol sistem</p>
                @endforelse
            </div>
        </x-card>
    </div>

    <!-- Durasi Pompa & Sensor Readings -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Durasi Pompa -->
        <x-card class="plant-card lg:col-span-2 border-0" shadow separator>
            <x-slot:title>Durasi Pompa Harian</x-slot:title>
            <div class="grid gap-5 lg:grid-cols-[1fr_220px]">
                <div class="space-y-3">
                    @foreach($pumpDurationData as $day)
                        <div class="grid grid-cols-[2rem_1fr_2.5rem] items-center gap-3">
                            <span class="text-xs font-medium">{{ $day['day'] }}</span>
                            <div class="h-3 overflow-hidden rounded bg-base-200/80">
                                <div class="h-full rounded bg-gradient-to-r from-green-400 to-cyan-400" style="width: {{ ($day['duration'] / 30) * 100 }}%"></div>
                            </div>
                            <span class="text-right text-xs font-semibold">{{ $day['duration'] }}m</span>
                        </div>
                    @endforeach
                </div>

                <div class="rounded-[1.5rem] border border-base-content/10 bg-base-200/60 p-4">
                    <p class="mb-3 text-xs font-semibold text-base-content/60">Profil Sensor Saat Ini</p>
                    <div class="space-y-4">
                        @forelse($sensorBands as $band)
                            <div>
                                <div class="mb-1 flex items-center justify-between gap-3">
                                    <span class="text-xs font-medium text-base-content/70">{{ $band['label'] }}</span>
                                    <span class="text-sm font-bold">{{ $band['value'] }}</span>
                                </div>
                                <div class="h-2 overflow-hidden rounded bg-base-100">
                                    <div class="h-full rounded bg-gradient-to-r {{ $band['color'] }}" style="width: {{ $band['percent'] }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-base-content/60">Belum ada pembacaan sensor</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </x-card>

        <!-- Sensor Readings Summary -->
        <x-card class="plant-card border-0" shadow separator>
            <x-slot:title>Batasan Pembacaan</x-slot:title>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between py-2 border-b border-base-content/20">
                    <span class="text-base-content/70">Kelembaban min</span>
                    <span class="font-semibold">40%</span>
                </div>
                <div class="flex justify-between py-2 border-b border-base-content/20">
                    <span class="text-base-content/70">Kelembaban maks</span>
                    <span class="font-semibold">80%</span>
                </div>
                <div class="flex justify-between py-2 border-b border-base-content/20">
                    <span class="text-base-content/70">Cahaya min</span>
                    <span class="font-semibold">30%</span>
                </div>
                <div class="flex justify-between py-2 border-b border-base-content/20">
                    <span class="text-base-content/70">Cahaya maks</span>
                    <span class="font-semibold">90%</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-base-content/70">TDS maksimum</span>
                    <span class="font-semibold">800ppm</span>
                </div>
            </div>
        </x-card>
    </div>

    <!-- Activity Logs -->
    <x-card class="plant-card border-0" shadow separator>
        <x-slot:title>Log Aktivitas Sistem</x-slot:title>
        <div class="space-y-3">
            @forelse($activityLogs as $log)
                <div class="flex items-start gap-4 py-2 border-b border-base-content/20 last:border-b-0">
                    <div class="pt-1">
                        @switch($log->type)
                            @case('PUMP')
                                <div class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-100">
                                    <x-icon name="o-power" class="w-3 h-3 text-red-600" />
                                </div>
                                @break
                            @case('DATA')
                                <div class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-100">
                                    <x-icon name="o-cloud-arrow-up" class="w-3 h-3 text-blue-600" />
                                </div>
                                @break
                            @case('ALERT')
                                <div class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-orange-100">
                                    <x-icon name="o-exclamation-triangle" class="w-3 h-3 text-orange-600" />
                                </div>
                                @break
                            @default
                                <div class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-100">
                                    <x-icon name="o-check" class="w-3 h-3 text-gray-600" />
                                </div>
                        @endswitch
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-base-content">{{ $log->message }}</p>
                        <p class="text-xs text-base-content/60">{{ $log->occurred_at->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-base-content/60 text-center py-4">Tidak ada aktivitas</p>
            @endforelse
        </div>
    </x-card>
</div>
