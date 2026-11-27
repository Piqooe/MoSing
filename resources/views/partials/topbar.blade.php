@php($initials = \App\Support\Format::initials($currentUser->name))

<header data-topbar
    class="sticky top-0 z-30 hidden h-16 items-center justify-between gap-6 px-6 transition-colors duration-200 data-scrolled:bg-content/95 md:flex">
    <div class="flex gap-2">
        <button type="button" data-history="back" aria-label="Go back"
            class="flex size-8 items-center justify-center rounded-full bg-black/70 text-white transition hover:bg-black">
            <x-lucide-chevron-left class="size-[18px]" />
        </button>
        <button type="button" data-history="forward" aria-label="Go forward"
            class="flex size-8 items-center justify-center rounded-full bg-black/70 text-muted transition hover:bg-black hover:text-white">
            <x-lucide-chevron-right class="size-[18px]" />
        </button>
    </div>

    @hasSection('topbar-center')
        <div class="flex min-w-0 flex-1 justify-center">
            @yield('topbar-center')
        </div>
    @endif

    <div class="flex items-center gap-3">
        <button type="button" aria-label="Notifications"
            class="flex size-8 items-center justify-center rounded-full bg-black/70 text-white transition hover:scale-105 hover:bg-black">
            <x-lucide-bell class="size-4" />
        </button>

        @auth
            <div class="relative" data-account-menu>
                <button type="button" aria-label="Account menu" aria-haspopup="menu" aria-expanded="false" data-account-toggle
                    class="flex h-8 items-center gap-2 rounded-full bg-black/70 py-0.5 pr-3 pl-0.5 transition hover:bg-black">
                    @if ($currentUser->avatar_url ?? null)
                        <img src="{{ $currentUser->avatar_url }}" alt="" class="size-7 rounded-full object-cover">
                    @else
                        <span class="flex size-7 items-center justify-center rounded-full bg-linear-to-br from-[#8B5CF6] to-[#1E1B4B] text-[11px] font-bold">{{ $initials }}</span>
                    @endif
                    <span class="text-sm font-semibold">{{ $currentUser->name }}</span>
                    <x-lucide-chevron-down class="size-4 text-text-secondary" />
                </button>

                <div data-account-panel hidden role="menu"
                    class="absolute top-10 right-0 z-40 min-w-40 rounded-md bg-toast p-1 shadow-[0_8px_24px_rgb(0_0_0/0.55)] ring-1 ring-white/10">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" role="menuitem"
                            class="flex w-full items-center gap-2 rounded px-3 py-2 text-left text-sm text-white transition hover:bg-card-hover">
                            <x-lucide-log-out class="size-4" />
                            Log out
                        </button>
                    </form>
                </div>
            </div>
        @else
            <a href="{{ route('login') }}"
                class="rounded-lg bg-accent px-5 py-2 text-sm font-bold whitespace-nowrap text-black transition hover:brightness-110">
                Log in
            </a>
        @endauth
    </div>
</header>

<script>
    (() => {
        const menu = document.querySelector('[data-account-menu]');
        if (!menu) return;

        const toggle = menu.querySelector('[data-account-toggle]');
        const panel = menu.querySelector('[data-account-panel]');
        const setOpen = (open) => {
            panel.hidden = !open;
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        };

        toggle.addEventListener('click', (event) => {
            event.stopPropagation();
            setOpen(panel.hidden);
        });
        document.addEventListener('click', (event) => {
            if (!menu.contains(event.target)) setOpen(false);
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') setOpen(false);
        });
    })();
</script>
