<?php

Route::livewire('/', 'pages::auth.login')->name('login');
Route::get('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('login');
})->name('logout');

Route::livewire('/admin/dashboard', 'pages::admin.idx')->name('admin');
Route::livewire('/monitoring', 'pages::monitoring.dashboard')->name('monitoring');
Route::livewire('/data-tanaman', 'pages::plants.idx')->name('plants');
Route::livewire('/help', 'pages::help.idx')->name('help');
