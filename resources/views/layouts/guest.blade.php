<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#121212">
    <title>@yield('title') · MoSing</title>

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-dvh bg-bg font-sans text-white antialiased">
    <main class="mx-auto flex min-h-dvh w-full max-w-[340px] flex-col justify-center px-4 py-10">
        <div class="w-full">
            <div class="mb-8 flex items-center justify-center gap-2.5">
                <span class="flex size-9 items-center justify-center rounded-lg bg-accent">
                    <x-lucide-audio-waveform class="size-5 text-black" />
                </span>
                <span class="text-[22px] font-bold tracking-[-0.4px]">MoSing</span>
            </div>

            @yield('content')
        </div>
    </main>
</body>
</html>
