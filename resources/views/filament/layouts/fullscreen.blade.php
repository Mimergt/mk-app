<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ __('filament-panels::layout.direction') ?? 'ltr' }}"
    @class([
        'fi min-h-screen',
        'dark' => filament()->hasDarkModeForced(),
    ])
>
    <head>
        <meta charset="utf-8" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <title>
            {{ filled($title = strip_tags(($livewire ?? null)?->getTitle() ?? '')) ? "{$title} - " : null }}
            {{ config('app.name') }}
        </title>

        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>

        @filamentStyles
        @vite('resources/css/app.css')
    </head>

    <body class="fi-body fi-panel-app min-h-screen bg-gray-50 font-normal text-gray-950 antialiased dark:bg-gray-950 dark:text-white">
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::BODY_START, scopes: $livewire->getRenderHookScopes()) }}

        <div class="fi-layout flex min-h-screen w-full overflow-x-clip">
            <div class="fi-main-ctn flex w-full flex-col opacity-0">
                <main class="fi-main mx-auto h-full w-full px-4 py-6 md:px-6 md:py-8">
                    {{ $slot }}
                </main>
            </div>
        </div>

        @livewire(\Filament\Livewire\Notifications::class)

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::BODY_END, scopes: $livewire->getRenderHookScopes()) }}

        @filamentScripts
        @vite('resources/js/app.js')

        <script>
            // Auto-refresh cada minuto para actualizar contador
            setInterval(function() {
                if (typeof Livewire !== 'undefined') {
                    Livewire.dispatch('refresh-timer');
                }
            }, 1000);
        </script>
    </body>
</html>
