@extends('layouts.app')

@section('title', 'Search')

@php
    $filters = ['all' => 'All', 'songs' => 'Songs', 'artists' => 'Artists', 'albums' => 'Albums', 'playlists' => 'Playlists', 'genres' => 'Genres'];
    $heading = 'text-[17px] font-bold tracking-[-0.3px] md:text-[22px]';
@endphp

@section('topbar-center')
    <form role="search" action="{{ route('search') }}" data-search-form class="w-full max-w-[440px]">
        <label
            class="group flex h-12 items-center gap-3 rounded-full border border-[#3a3a3a] bg-toast px-[18px] transition-colors hover:border-outline focus-within:border-accent focus-within:ring-1 focus-within:ring-accent">
            <x-lucide-search class="size-5 shrink-0 text-text-secondary group-focus-within:text-white" />
            <input data-search-input type="search" name="q" autocomplete="off" spellcheck="false"
                placeholder="What do you want to listen to?" value="{{ request('q') }}" aria-label="Search"
                class="w-full min-w-0 bg-transparent text-[15px] text-white outline-none placeholder:text-muted [&::-webkit-search-cancel-button]:appearance-none">
            <button type="button" data-search-clear aria-label="Clear search" hidden
                class="flex size-6 shrink-0 items-center justify-center text-text-secondary transition-colors hover:text-white">
                <x-lucide-x class="size-5" />
            </button>
        </label>
    </form>
@endsection

@section('content')
    <section class="px-4 pt-3 pb-2 md:hidden">
        <div class="flex items-center gap-3">
            <span
                class="flex size-8 shrink-0 items-center justify-center rounded-full bg-linear-to-br from-[#8B5CF6] to-[#1E1B4B] text-xs font-bold">
                {{ \App\Support\Format::initials($currentUser->name) }}
            </span>
            <h1 class="text-2xl font-bold tracking-[-0.4px]">Search</h1>
        </div>

        <form role="search" action="{{ route('search') }}" data-search-form class="mt-4">
            <label
                class="flex h-12 items-center gap-3 rounded-lg bg-white px-4 focus-within:ring-2 focus-within:ring-accent">
                <x-lucide-search class="size-5 shrink-0 text-black" />
                <input data-search-input type="search" name="q" autocomplete="off" spellcheck="false"
                    placeholder="What do you want to listen to?" value="{{ request('q') }}" aria-label="Search"
                    class="w-full min-w-0 bg-transparent text-[15px] font-medium text-black outline-none placeholder:text-[#535353] [&::-webkit-search-cancel-button]:appearance-none">
                <button type="button" data-search-clear aria-label="Clear search" hidden
                    class="flex size-6 shrink-0 items-center justify-center text-[#535353] hover:text-black">
                    <x-lucide-x class="size-5" />
                </button>
            </label>
        </form>
    </section>

    <p id="search-status" class="sr-only" role="status" aria-live="polite"></p>

    <div class="flex flex-col gap-6 px-4 pt-2 pb-10 md:gap-8 md:px-6">
        <div id="search-filters" hidden role="toolbar" aria-label="Filter results"
            class="-mx-4 flex gap-2 overflow-x-auto px-4 scrollbar-none md:mx-0 md:px-0">
            @foreach ($filters as $key => $label)
                <button type="button" data-filter="{{ $key }}" aria-pressed="{{ $loop->first ? 'true' : 'false' }}"
                    class="inline-flex h-[30px] shrink-0 items-center rounded-full bg-card px-3.5 text-[13px] font-semibold whitespace-nowrap text-white transition-colors hover:bg-card-hover aria-pressed:bg-accent aria-pressed:text-black">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <div id="search-browse" class="flex flex-col gap-6 md:gap-10">
            <section id="recent-section" hidden class="flex flex-col gap-3 md:gap-4">
                <div class="flex items-end justify-between gap-4">
                    <h2 class="{{ $heading }}">Recent searches</h2>
                    <button type="button" id="recent-clear"
                        class="text-xs font-semibold text-text-secondary transition-colors hover:text-white hover:underline md:text-[13px]">Clear
                        all</button>
                </div>
                <div id="recent-list"
                    class="-mx-4 flex gap-3 overflow-x-auto px-4 scrollbar-none md:mx-0 md:grid md:grid-cols-4 md:overflow-visible md:px-0 xl:grid-cols-6 md:max-xl:[&>*:nth-child(n+5)]:hidden">
                </div>
            </section>

            <section id="genres" class="flex flex-col gap-3 md:gap-4">
                <h2 class="{{ $heading }}">Browse all</h2>
                <div class="grid grid-cols-2 gap-3 md:grid-cols-3 md:gap-4 xl:grid-cols-4">
                    @foreach ($genres as $genre)
                        <x-genre-card :genre="$genre" />
                    @endforeach
                </div>
            </section>
        </div>

        <div id="search-results" hidden class="flex flex-col gap-8 md:gap-10"></div>
    </div>

    <template id="tpl-genres-compact">
        @foreach ($genres as $genre)
            <x-genre-card :genre="$genre" compact />
        @endforeach
    </template>

    <template id="tpl-empty">
        <div class="flex flex-col items-center px-4 py-12 text-center md:pt-28">
            <div class="flex size-[88px] items-center justify-center rounded-full bg-card">
                <x-lucide-search class="size-9 text-text-secondary" />
            </div>
            <h2 data-empty-title class="mt-4 text-xl font-bold md:text-2xl"></h2>
            <p class="mt-3 max-w-[460px] text-[15px] leading-[22px] text-text-secondary">
                Please make sure your words are spelled correctly, or use fewer or different keywords.
            </p>
            <x-button variant="secondary" data-browse-genres class="mt-6">Browse genres</x-button>
        </div>
    </template>

    <template id="tpl-error">
        <div class="flex flex-col items-center px-4 py-12 text-center md:pt-28">
            <div class="flex size-[88px] items-center justify-center rounded-full bg-card">
                <x-lucide-wifi-off class="size-9 text-text-secondary" />
            </div>
            <h2 class="mt-4 text-xl font-bold md:text-2xl">Something went wrong</h2>
            <p class="mt-3 max-w-[460px] text-[15px] leading-[22px] text-text-secondary">We couldn't load the results.
                Please try again.</p>
            <x-button variant="secondary" data-retry class="mt-6">Try again</x-button>
        </div>
    </template>

    <template id="tpl-loading">
        <div class="grid gap-6 xl:grid-cols-[430px_minmax(0,1fr)]" aria-hidden="true">
            <div class="hidden flex-col gap-4 xl:flex">
                <div class="h-7 w-32 rounded bg-skeleton"></div>
                <div class="relative h-[236px] overflow-hidden rounded-lg bg-card">
                    <div
                        class="absolute inset-0 motion-safe:animate-shimmer bg-linear-to-r from-transparent via-white/6 to-transparent">
                    </div>
                </div>
            </div>
            <div class="flex flex-col gap-4">
                <div class="h-7 w-24 rounded bg-skeleton"></div>
                @for ($i = 0; $i < 4; $i++)
                    <div class="flex h-14 items-center gap-3.5 px-3">
                        <div class="relative size-10 overflow-hidden rounded bg-card-hover">
                            <div
                                class="absolute inset-0 motion-safe:animate-shimmer bg-linear-to-r from-transparent via-white/6 to-transparent">
                            </div>
                        </div>
                        <div class="flex flex-1 flex-col gap-2">
                            <div class="h-3.5 w-1/3 rounded bg-skeleton"></div>
                            <div class="h-3 w-1/5 rounded bg-card-hover"></div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>
        <section class="flex flex-col gap-4" aria-hidden="true">
            <div class="h-7 w-28 rounded bg-skeleton"></div>
            <div
                class="-mx-4 flex gap-3.5 overflow-hidden px-4 md:mx-0 md:grid md:grid-cols-4 md:gap-3 md:px-0 xl:grid-cols-6 md:max-xl:[&>*:nth-child(n+5)]:hidden">
                @for ($i = 0; $i < 6; $i++)
                    <x-music-card-skeleton :circle="true" />
                @endfor
            </div>
        </section>
    </template>

    <template id="icon-play"><x-lucide-play class="size-[18px] fill-current" /></template>
    <template id="icon-play-sm"><x-lucide-play class="size-4 fill-current text-white" /></template>
    <template id="icon-heart"><x-lucide-heart class="size-[18px]" /></template>
    <template id="icon-x"><x-lucide-x class="size-3" /></template>

    <script>
        (() => {
            const inputs = [...document.querySelectorAll('[data-search-input]')];
            const clearButtons = document.querySelectorAll('[data-search-clear]');
            const status = document.getElementById('search-status');
            const filtersBar = document.getElementById('search-filters');
            const filterButtons = [...filtersBar.querySelectorAll('[data-filter]')];
            const browse = document.getElementById('search-browse');
            const results = document.getElementById('search-results');
            const recentSection = document.getElementById('recent-section');
            const recentList = document.getElementById('recent-list');
            const scrollRoot = document.querySelector('[data-scroll-root]');

            const endpoint = @json(url('/api/search'));
            const palettes = @json(\App\Support\Artwork::PALETTES);
            const patterns = @json(\App\Support\Artwork::PATTERNS);
            const paletteNames = Object.keys(palettes);

            const DEBOUNCE_MS = 300;
            const RECENT_KEY = 'mosing:recent-searches';
            const RECENT_MAX = 12;

            const state = { query: null, data: null, filter: 'all', items: new Map() };
            let timer = null;
            let controller = null;

            const tpl = (id) => document.getElementById(id).content.cloneNode(true);
            const icon = (name) => document.getElementById(`icon-${name}`).innerHTML;

            const esc = (value) => String(value ?? '').replace(/[&<>"']/g, (c) => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
            })[c]);

            const duration = (seconds) => {
                const s = Math.max(0, parseInt(seconds, 10) || 0);
                return `${Math.floor(s / 60)}:${String(s % 60).padStart(2, '0')}`;
            };

            const crcTable = Array.from({ length: 256 }, (_, n) => {
                let c = n;
                for (let k = 0; k < 8; k++) c = c & 1 ? 0xEDB88320 ^ (c >>> 1) : c >>> 1;
                return c >>> 0;
            });
            const crc32 = (str) => {
                let crc = 0xFFFFFFFF;
                for (const byte of new TextEncoder().encode(str)) crc = crcTable[(crc ^ byte) & 0xFF] ^ (crc >>> 8);
                return (crc ^ 0xFFFFFFFF) >>> 0;
            };

            const resolveArt = (art, seed) => {
                const hash = crc32(seed || 'mosing');
                const pattern = art?.pattern ?? patterns[hash % patterns.length];
                const name = art?.palette ?? paletteNames[Math.floor(hash / 7) % paletteNames.length];
                const [dark, accent] = palettes[name] ?? palettes.blue;
                return { pattern, dark, accent };
            };

            const cover = ({ src, art, seed = '', label = null, artist = false, classes = '' }) => {
                if (src) {
                    return `<div class="relative shrink-0 overflow-hidden bg-card ${classes}">
                            <img src="${esc(src)}" alt="${esc(seed)}" loading="lazy" class="absolute inset-0 size-full object-cover">
                        </div>`;
                }

                const { pattern, dark, accent } = resolveArt(art, seed);
                const bg = artist
                    ? `radial-gradient(ellipse 90% 90% at 35% 30%, ${accent} 0%, ${dark} 100%)`
                    : `linear-gradient(135deg, ${dark} 0%, ${accent}b3 100%)`;

                let shapes;
                if (artist) {
                    shapes = `<circle cx="50" cy="39" r="17" fill="#fff" fill-opacity=".18" />
                                  <ellipse cx="50" cy="92" rx="37" ry="30" fill="#fff" fill-opacity=".14" />`;
                } else if (pattern === 'orb') {
                    shapes = `<circle cx="73" cy="27" r="39" fill="${accent}" fill-opacity=".8" />
                                  <circle cx="25" cy="73" r="15" fill="#fff" fill-opacity=".2" />`;
                } else if (pattern === 'bands') {
                    shapes = [[52, .9], [64, .7], [76, .5], [88, .3]]
                        .map(([y, o]) => `<rect x="0" y="${y}" width="100" height="6" fill="${accent}" fill-opacity="${o}" />`).join('')
                        + `<circle cx="50" cy="34" r="18" fill="#fff" fill-opacity=".9" />`;
                } else if (pattern === 'rings') {
                    shapes = [12.5, 23.5, 34.5, 45.5]
                        .map((r) => `<circle cx="62" cy="42" r="${r}" fill="none" stroke="#fff" stroke-opacity=".25" stroke-width="1.1" />`).join('')
                        + `<circle cx="62" cy="42" r="8" fill="${accent}" />`;
                } else {
                    shapes = `<ellipse cx="50" cy="107" rx="55" ry="45" fill="${accent}" fill-opacity=".85" />
                                  <line x1="0" y1="60" x2="100" y2="60" stroke="#fff" stroke-opacity=".35" stroke-width="1" vector-effect="non-scaling-stroke" />
                                  <circle cx="77" cy="23" r="9" fill="#fff" fill-opacity=".85" />`;
                }

                return `<div class="relative shrink-0 overflow-hidden bg-card ${classes}">
                        <div class="absolute inset-0" style="background: ${bg}" aria-hidden="true">
                            <svg viewBox="0 0 100 100" class="absolute inset-0 size-full" preserveAspectRatio="none">${shapes}</svg>
                            ${label ? `<span class="absolute top-[8%] left-[8%] max-w-[80%] text-[10px] leading-tight font-bold tracking-[0.6px] text-white/90 uppercase">${esc(label)}</span>` : ''}
                        </div>
                    </div>`;
            };

            const pageUrl = {
                artist: @json(url('/artists')),
                album: @json(url('/albums')),
                playlist: @json(url('/playlists')),
            };

            const toItem = {
                song: (s) => ({
                    key: `song-${s.id}`, id: s.id, type: 'song', title: s.title, subtitle: s.artist?.name ?? '',
                    src: s.album?.cover_url, art: s.album?.artwork ?? s.artist?.artwork, seed: s.album?.title ?? s.title,
                    duration: s.duration,
                }),
                artist: (a) => ({
                    key: `artist-${a.id}`, type: 'artist', title: a.name, subtitle: 'Artist', href: `${pageUrl.artist}/${a.slug}`,
                    src: a.photo_url, art: a.artwork, seed: a.name,
                }),
                album: (a) => ({
                    key: `album-${a.id}`, type: 'album', title: a.title, href: `${pageUrl.album}/${a.slug}`,
                    subtitle: [a.release_year, a.artist?.name].filter(Boolean).join(' • '),
                    src: a.cover_url, art: a.artwork, seed: a.title,
                }),
                playlist: (p) => ({
                    key: `playlist-${p.id}`, type: 'playlist', title: p.title, href: `${pageUrl.playlist}/${p.slug}`,
                    subtitle: `Playlist • ${p.songs_count ?? 0} songs`,
                    src: p.cover_url, art: p.artwork, seed: p.title,
                }),
            };

            const typeLabel = { song: 'Song', artist: 'Artist', album: 'Album', playlist: 'Playlist' };

            const coverFor = (item, classes) => cover({
                src: item.src, art: item.art, seed: item.seed, artist: item.type === 'artist',
                label: ['album', 'playlist'].includes(item.type) ? item.title : null,
                classes: `${classes} ${item.type === 'artist' ? 'rounded-full' : 'rounded-md'}`,
            });

            const remember = (item) => {
                state.items.set(item.key, item);
                return item;
            };

            const loadRecent = () => {
                try { return JSON.parse(localStorage.getItem(RECENT_KEY)) || []; } catch { return []; }
            };
            const saveRecent = (list) => {
                try { localStorage.setItem(RECENT_KEY, JSON.stringify(list.slice(0, RECENT_MAX))); } catch { }
            };
            const addRecent = (item) => {
                const { key, type, title, subtitle, src, art, seed } = item;
                saveRecent([{ key, type, title, subtitle, src, art, seed }, ...loadRecent().filter((r) => r.key !== key)]);
            };

            const renderRecent = () => {
                const list = loadRecent();
                recentSection.hidden = list.length === 0;
                recentList.innerHTML = list.map((item) => card(item, {
                    recent: true,
                    subtitle: item.type === 'artist' ? 'Artist' : `${typeLabel[item.type]} • ${item.subtitle}`,
                })).join('');
            };

            const heading = (title, filter = null) => `
                    <div class="flex items-end justify-between gap-4">
                        <h2 class="text-[17px] font-bold tracking-[-0.3px] md:text-[22px]">${esc(title)}</h2>
                        ${filter ? `<button type="button" data-show="${filter}" class="shrink-0 text-xs font-semibold text-text-secondary transition-colors hover:text-white hover:underline md:text-[13px]">Show all</button>` : ''}
                    </div>`;

            const card = (item, { grid = false, recent = false, subtitle = item.subtitle } = {}) => {
                const circle = item.type === 'artist';
                return `
                    <article class="group relative flex shrink-0 flex-col gap-1.5 rounded-lg transition-colors duration-200 md:p-3 ${grid ? 'w-auto' : 'w-[140px] md:w-auto'} ${circle && !recent ? 'md:hover:bg-card' : 'md:bg-card md:hover:bg-card-hover'}">
                        <div class="relative">
                            ${coverFor(item, 'aspect-square w-full')}
                            ${recent
                        ? `<button type="button" data-recent-remove="${esc(item.key)}" aria-label="Remove ${esc(item.title)} from recent searches"
                                        class="absolute top-2 right-2 z-20 flex size-6 items-center justify-center rounded-full bg-black/70 text-white transition hover:scale-110 hover:bg-black">${icon('x')}</button>`
                        : `<button type="button" aria-label="Play ${esc(item.title)}"
                                        class="shadow-play absolute right-2 bottom-2 z-20 hidden size-11 translate-y-2 items-center justify-center rounded-full bg-accent pl-[3px] text-black opacity-0 transition duration-200 ease-out group-hover:translate-y-0 group-hover:opacity-100 hover:scale-105 focus-visible:translate-y-0 focus-visible:opacity-100 md:inline-flex">${icon('play')}</button>`}
                        </div>
                        <a href="${esc(item.href ?? '#')}" data-item="${esc(item.key)}" ${recent ? `data-recent-open="${esc(item.title)}"` : ''}
                            class="truncate text-[13px] font-semibold text-white outline-none after:absolute after:inset-0 after:z-10 after:rounded-lg focus-visible:after:outline-2 focus-visible:after:outline-accent md:text-[15px] ${circle ? 'text-center' : ''}">${esc(item.title)}</a>
                        <p class="line-clamp-2 text-xs leading-[15px] text-text-secondary md:text-[13px] md:leading-[18px] ${circle ? 'text-center' : ''}">${esc(subtitle)}</p>
                    </article>`;
            };

            const songRow = (item) => `
                    <div class="group relative grid h-14 grid-cols-[40px_minmax(0,1fr)_auto_auto] items-center gap-3.5 rounded-md px-3 transition-colors duration-150 hover:bg-card-hover">
                        <div class="relative size-10">
                            ${cover({ src: item.src, art: item.art, seed: item.seed, classes: 'size-10 rounded' })}
                            <span class="absolute inset-0 hidden items-center justify-center rounded bg-black/60 group-hover:flex" aria-hidden="true">${icon('play-sm')}</span>
                        </div>
                        <div class="flex min-w-0 flex-col gap-1">
                            <a href="${esc(item.href ?? '#')}" data-item="${esc(item.key)}" class="truncate text-[15px] leading-[18px] text-white outline-none after:absolute after:inset-0 after:rounded-md focus-visible:after:outline-2 focus-visible:after:outline-accent">${esc(item.title)}</a>
                            <span class="truncate text-[13px] leading-4 text-text-secondary group-hover:text-white">${esc(item.subtitle)}</span>
                        </div>
                        <button type="button" data-song-like data-song-id="${esc(item.id)}" data-song-title="${esc(item.title)}" data-song-liked="false"
                            aria-label="Save ${esc(item.title)} to Liked Songs" aria-pressed="false"
                            class="relative z-10 flex size-8 items-center justify-center text-text-secondary opacity-0 transition hover:scale-105 hover:text-white group-hover:opacity-100 focus-visible:opacity-100">${icon('heart')}</button>
                        <span class="w-10 text-right text-sm text-text-secondary tabular-nums">${duration(item.duration)}</span>
                    </div>`;

            const topResult = (item) => `
                    <section class="flex flex-col gap-3 md:gap-4">
                        ${heading('Top result')}
                        <article class="group relative flex flex-col rounded-lg bg-card p-5 transition-colors duration-200 hover:bg-card-hover xl:h-[236px] xl:bg-card-hover xl:hover:bg-[#363636]">
                            ${coverFor(item, 'size-24 shadow-[0_8px_24px_rgb(0_0_0/0.5)]')}
                            <a href="${esc(item.href ?? '#')}" data-item="${esc(item.key)}"
                                class="mt-5 truncate pr-16 text-[28px] leading-tight font-bold tracking-[-0.8px] text-white outline-none after:absolute after:inset-0 after:rounded-lg focus-visible:after:outline-2 focus-visible:after:outline-accent md:text-[32px]">${esc(item.title)}</a>
                            <div class="mt-2 flex min-w-0 items-center gap-2 pr-16 text-sm text-text-secondary">
                                <span class="rounded-full bg-black/70 px-2.5 py-1 text-[13px] font-semibold text-white">${typeLabel[item.type]}</span>
                                ${item.type !== 'artist' && item.subtitle ? `<span class="truncate">${esc(item.subtitle)}</span>` : ''}
                            </div>
                            <button type="button" aria-label="Play ${esc(item.title)}"
                                class="shadow-play absolute right-5 bottom-5 z-10 inline-flex size-12 items-center justify-center rounded-full bg-accent pl-[3px] text-black transition hover:scale-105 hover:brightness-110">${icon('play')}</button>
                        </article>
                    </section>`;

            const songsSection = (songs, { limit = 4, showAll = false } = {}) => `
                    <section class="flex min-w-0 flex-col gap-3 md:gap-4">
                        ${heading('Songs', showAll && songs.length > limit ? 'songs' : null)}
                        <div class="-mx-3 flex flex-col">${songs.slice(0, limit).map(songRow).join('')}</div>
                    </section>`;

            const rail = 'flex gap-3.5 overflow-x-auto snap-x snap-mandatory scroll-px-4 -mx-4 px-4 scrollbar-none *:snap-start md:mx-0 md:grid md:grid-cols-4 md:gap-3 md:overflow-visible md:px-0 xl:grid-cols-6 md:max-xl:[&>*:nth-child(n+5)]:hidden';
            const gridCls = 'grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-6';

            const cardsSection = (title, items, filter, { grid = false } = {}) => `
                    <section class="flex flex-col gap-3 md:gap-4">
                        ${heading(title, grid ? null : filter)}
                        <div class="${grid ? gridCls : rail}">${items.map((i) => card(i, { grid })).join('')}</div>
                    </section>`;

            const genreMatches = (q) => {
                const needle = q.toLowerCase();
                return [...tpl('tpl-genres-compact').querySelectorAll('[data-genre]')]
                    .filter((el) => el.dataset.genre.toLowerCase().includes(needle));
            };

            const genresSection = (nodes, { limit = 4 } = {}) => {
                const section = document.createElement('section');
                section.className = 'flex flex-col gap-3 md:gap-4';
                section.innerHTML = `${heading('Genres & Moods')}<div class="grid grid-cols-2 gap-3 md:grid-cols-3 md:gap-4 xl:grid-cols-4"></div>`;
                section.lastElementChild.append(...nodes.slice(0, limit));
                return section;
            };

            const score = (text, q) => {
                const t = String(text ?? '').toLowerCase();
                return t === q ? 3 : t.startsWith(q) ? 2 : t.includes(q) ? 1 : 0;
            };

            const pickTop = (groups, q) => {
                const needle = q.toLowerCase();
                let best = null;
                for (const item of [...groups.artists, ...groups.songs, ...groups.albums, ...groups.playlists]) {
                    const s = score(item.title, needle);
                    if (s > (best?.s ?? 0)) best = { item, s };
                }
                return best?.item ?? null;
            };

            const showEmpty = (title) => {
                const node = tpl('tpl-empty');
                node.querySelector('[data-empty-title]').textContent = title;
                results.replaceChildren(node);
            };

            const render = () => {
                const q = state.query;
                const raw = state.data ?? {};
                state.items.clear();

                const groups = {
                    songs: (raw.songs ?? []).map(toItem.song).map(remember),
                    artists: (raw.artists ?? []).map(toItem.artist).map(remember),
                    albums: (raw.albums ?? []).map(toItem.album).map(remember),
                    playlists: (raw.playlists ?? []).map(toItem.playlist).map(remember),
                };
                const genres = genreMatches(q);
                const total = groups.songs.length + groups.artists.length + groups.albums.length + groups.playlists.length + genres.length;

                if (total === 0) {
                    showEmpty(`No results found for “${q}”`);
                    status.textContent = `No results for ${q}`;
                    return;
                }

                const f = state.filter;
                const parts = [];

                if (f === 'all') {
                    const top = pickTop(groups, q);
                    if (top || groups.songs.length) {
                        parts.push(`<div class="grid gap-8 xl:grid-cols-[430px_minmax(0,1fr)] xl:gap-6">
                                ${top ? topResult(top) : ''}
                                ${groups.songs.length ? songsSection(groups.songs, { showAll: true }) : ''}
                            </div>`);
                    }
                    if (groups.artists.length) parts.push(cardsSection('Artists', groups.artists.slice(0, 6), 'artists'));
                    if (groups.albums.length) parts.push(cardsSection('Albums', groups.albums.slice(0, 6), 'albums'));
                    if (groups.playlists.length) parts.push(cardsSection('Playlists', groups.playlists.slice(0, 6), 'playlists'));
                } else if (f === 'songs' && groups.songs.length) {
                    parts.push(songsSection(groups.songs, { limit: Infinity }));
                } else if (groups[f]?.length) {
                    const title = filterButtons.find((b) => b.dataset.filter === f).textContent.trim();
                    parts.push(cardsSection(title, groups[f], f, { grid: true }));
                }

                const showGenres = (f === 'all' || f === 'genres') && genres.length;

                if (!parts.length && !showGenres) {
                    const label = filterButtons.find((b) => b.dataset.filter === f).textContent.trim().toLowerCase();
                    showEmpty(`No ${label} found for “${q}”`);
                    status.textContent = `No ${label} for ${q}`;
                    return;
                }

                results.innerHTML = parts.join('');
                window.MoSingLikes?.initialize(results);
                if (showGenres) results.append(genresSection(genres, { limit: f === 'genres' ? Infinity : 4 }));
                status.textContent = `${total} results for ${q}`;
            };

            const setMode = (mode) => {
                const browsing = mode === 'browse';
                browse.hidden = !browsing;
                results.hidden = browsing;
                filtersBar.hidden = browsing;
                if (browsing) renderRecent();
            };

            const setFilter = (filter) => {
                state.filter = filter;
                filterButtons.forEach((b) => b.setAttribute('aria-pressed', String(b.dataset.filter === filter)));
            };

            const syncInputs = (value, source = null) => {
                inputs.forEach((i) => { if (i !== source) i.value = value; });
                clearButtons.forEach((b) => { b.hidden = value === ''; });
            };

            const syncUrl = (q) => {
                const url = new URL(window.location.href);
                q ? url.searchParams.set('q', q) : url.searchParams.delete('q');
                history.replaceState(null, '', url);
            };

            const search = async (raw, { force = false } = {}) => {
                const q = raw.trim();
                if (q === state.query && !force) return;

                if (q !== state.query) setFilter('all');
                state.query = q;
                syncUrl(q);
                controller?.abort();

                if (q === '') {
                    state.data = null;
                    status.textContent = '';
                    setMode('browse');
                    return;
                }

                setMode('results');
                controller = new AbortController();
                results.replaceChildren(tpl('tpl-loading'));
                results.setAttribute('aria-busy', 'true');
                status.textContent = 'Searching…';

                try {
                    const res = await fetch(`${endpoint}?q=${encodeURIComponent(q)}`, {
                        headers: { Accept: 'application/json' },
                        signal: controller.signal,
                    });
                    if (!res.ok) throw new Error(`HTTP ${res.status}`);
                    state.data = (await res.json()).data;
                    render();
                } catch (err) {
                    if (err.name === 'AbortError') return;
                    console.error('Search failed:', err);
                    state.data = null;
                    results.replaceChildren(tpl('tpl-error'));
                    status.textContent = 'Search failed';
                } finally {
                    results.removeAttribute('aria-busy');
                }
            };

            const setQuery = (value, { focus = true } = {}) => {
                syncInputs(value);
                clearTimeout(timer);
                search(value);
                if (focus) visibleInput()?.focus();
                scrollRoot?.scrollTo({ top: 0 });
            };

            const visibleInput = () => inputs.find((i) => i.offsetParent !== null);

            inputs.forEach((input) => {
                input.addEventListener('input', () => {
                    syncInputs(input.value, input);
                    clearTimeout(timer);
                    timer = setTimeout(() => search(input.value), DEBOUNCE_MS);
                });
                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && input.value) {
                        e.preventDefault();
                        setQuery('');
                    }
                });
            });

            document.querySelectorAll('[data-search-form]').forEach((form) => {
                form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    clearTimeout(timer);
                    search(form.querySelector('[data-search-input]').value, { force: true });
                });
            });

            clearButtons.forEach((b) => b.addEventListener('click', () => setQuery('')));

            filterButtons.forEach((b) => b.addEventListener('click', () => {
                setFilter(b.dataset.filter);
                if (state.data) render();
            }));

            results.addEventListener('click', (e) => {
                const show = e.target.closest('[data-show]');
                if (show) {
                    setFilter(show.dataset.show);
                    render();
                    scrollRoot?.scrollTo({ top: 0, behavior: 'smooth' });
                    return;
                }
                if (e.target.closest('[data-browse-genres]')) return setQuery('', { focus: false });
                if (e.target.closest('[data-retry]')) return search(state.query ?? '', { force: true });

                const link = e.target.closest('[data-item]');
                const item = link && state.items.get(link.dataset.item);
                if (item) {
                    e.preventDefault();
                    addRecent(item);
                    if (item.href) window.location.href = item.href;
                }
            });

            recentList.addEventListener('click', (e) => {
                const remove = e.target.closest('[data-recent-remove]');
                if (remove) {
                    saveRecent(loadRecent().filter((r) => r.key !== remove.dataset.recentRemove));
                    renderRecent();
                    return;
                }
                const open = e.target.closest('[data-recent-open]');
                if (open) {
                    e.preventDefault();
                    setQuery(open.dataset.recentOpen);
                }
            });

            document.getElementById('recent-clear').addEventListener('click', () => {
                saveRecent([]);
                renderRecent();
            });

            syncInputs(inputs[0]?.value ?? '');
            search(inputs[0]?.value ?? '');
            if (!state.query) visibleInput()?.focus();
        })();
    </script>
@endsection