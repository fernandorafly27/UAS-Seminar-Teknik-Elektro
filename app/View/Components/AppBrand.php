<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AppBrand extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return <<<'HTML'
                <a href="{{ auth()->check() ? '/admin/dashboard' : '/' }}" wire:navigate>
                    <!-- Hidden when collapsed -->
                    <div {{ $attributes->class(["hidden-when-collapsed"]) }}>
                        <div class="plant-card flex items-center gap-3 rounded-[1.4rem] px-4 py-3">
                            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-600 via-lime-500 to-emerald-300 text-white shadow-lg shadow-emerald-900/10">
                                <svg class="h-7 w-7" viewBox="0 0 48 48" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M39.9 8.1C28.6 8.6 18.8 12.7 13.7 20.5C9.2 27.3 10.9 35.1 17 39C23.7 43.2 31.9 39.8 35.7 32.6C39.4 25.5 38.8 16.5 39.9 8.1Z" fill="currentColor" opacity="0.95"/>
                                    <path d="M9 39C16.8 29.7 24.9 23.7 34.8 18.8" stroke="#0f7a35" stroke-width="3" stroke-linecap="round"/>
                                    <path d="M22.4 29.1C22.3 23.6 20.1 18.6 16.5 15.3C10.8 16.7 6.6 20.4 5.6 25C4.3 31.2 8.7 36.4 15 36.8" fill="currentColor" opacity="0.72"/>
                                </svg>
                            </div>
                            <div class="me-3">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-base-content/45">Urban Growth</p>
                                <span class="page-title block text-2xl font-semibold text-primary">
                                    AgroVision
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Display when collapsed -->
                    <div class="display-when-collapsed plant-card hidden mx-4 mt-4 mb-1 flex h-14 w-14 items-center justify-center rounded-2xl">
                        <svg class="h-8 w-8 text-primary" viewBox="0 0 48 48" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                            <path d="M39.9 8.1C28.6 8.6 18.8 12.7 13.7 20.5C9.2 27.3 10.9 35.1 17 39C23.7 43.2 31.9 39.8 35.7 32.6C39.4 25.5 38.8 16.5 39.9 8.1Z" fill="currentColor" opacity="0.95"/>
                            <path d="M9 39C16.8 29.7 24.9 23.7 34.8 18.8" stroke="#0f7a35" stroke-width="3" stroke-linecap="round"/>
                            <path d="M22.4 29.1C22.3 23.6 20.1 18.6 16.5 15.3C10.8 16.7 6.6 20.4 5.6 25C4.3 31.2 8.7 36.4 15 36.8" fill="currentColor" opacity="0.72"/>
                        </svg>
                    </div>
                </a>
            HTML;
    }
}
