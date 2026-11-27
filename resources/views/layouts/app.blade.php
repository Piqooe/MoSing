<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#121212">
    <title>@hasSection('title') @yield('title') · MoSing @else MoSing – Music Streaming @endif</title>

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="overflow-hidden bg-bg font-sans text-white antialiased">
    <div class="flex h-dvh flex-col">
        <div class="flex min-h-0 flex-1 md:gap-2 md:p-2">
            @include('partials.sidebar')

            <main id="main" data-scroll-root
                class="relative isolate min-w-0 flex-1 overflow-x-hidden overflow-y-auto bg-bg md:rounded-lg md:bg-content">
                <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-[260px] bg-linear-to-b from-accent/18 to-transparent md:h-[185px] md:from-accent/22" aria-hidden="true"></div>

                @include('partials.topbar')

                @yield('content')

                @include('partials.footer')
            </main>
        </div>

        {{-- Nonaktifkan sementara player statis bawaan agar tidak dobel --}}
        {{-- @include('partials.mini-player') --}}
        {{-- @include('partials.player') --}}

        <x-now-playing-bar />

        @include('partials.bottom-nav')
    </div>

    <div data-toast role="status" aria-live="polite" hidden
        class="fixed top-4 right-4 z-[100] flex max-w-[calc(100vw-2rem)] items-center gap-3 rounded-lg bg-toast px-4 py-3 text-sm shadow-[0_8px_24px_rgb(0_0_0/0.55)] ring-1 ring-white/10 md:top-6 md:right-6">
        <x-lucide-circle-check class="size-4 shrink-0 text-success" />
        <span data-toast-message class="min-w-0 flex-1"></span>
        <button type="button" data-toast-undo hidden class="shrink-0 font-semibold text-accent hover:underline">Undo</button>
        <button type="button" data-toast-close aria-label="Dismiss notification"
            class="shrink-0 text-text-secondary transition hover:text-white">
            <x-lucide-x class="size-4" />
        </button>
    </div>

    @include('partials.sidebar-scroll')
</body>
</html>