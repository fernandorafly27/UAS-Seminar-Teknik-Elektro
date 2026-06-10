<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' - '.config('app.name') : config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="dashboard-surface min-h-screen font-sans antialiased">

    {{-- NAVBAR mobile only --}}
    <x-nav sticky class="plant-card mx-4 mt-4 rounded-[1.5rem] border-0 lg:hidden">
        <x-slot:brand>
            <x-app-brand />
        </x-slot:brand>
        <x-slot:actions>
            <label for="main-drawer" class="lg:hidden me-3">
                <x-icon name="o-bars-3" class="cursor-pointer" />
            </label>
        </x-slot:actions>
    </x-nav>

    {{-- MAIN --}}
    <x-main class="app-shell">
        {{-- SIDEBAR --}}
        <x-slot:sidebar drawer="main-drawer" collapsible class="app-sidebar rounded-[1.75rem] p-3 lg:min-h-[calc(100vh-2rem)]">

            {{-- BRAND --}}
            <x-app-brand class="px-4 pt-4" />

            {{-- MENU --}}
            <x-menu activate-by-route class="px-2 pb-3">

                <x-menu-item title="Home" icon="o-sparkles" link="/admin/dashboard" />
                <x-menu-item title="Monitoring" icon="o-chart-bar" link="/monitoring" />
                <x-menu-item title="Data Tanaman" icon="o-clipboard-document-list" link="/data-tanaman" />
                <x-menu-item title="Help" icon="o-question-mark-circle" link="/help" />
                <x-menu-item title="Exit" icon="o-arrow-right-on-rectangle" link="/logout" no-wire-navigate />

            </x-menu>
        </x-slot:sidebar>

        {{-- The `$slot` goes here --}}
        <x-slot:content>
            <div class="app-content">
                {{ $slot }}
            </div>
        </x-slot:content>
    </x-main>

    {{--  TOAST area --}}
    <x-toast />
</body>
</html>
