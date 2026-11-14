@props(['song', 'index' => 1, 'action' => 'Add'])

<div {{ $attributes->class([
    'group grid h-14 items-center gap-3 rounded-md px-2 transition-colors duration-150 hover:bg-card-hover md:gap-4 md:px-4',
    'grid-cols-[minmax(0,1fr)_auto] md:grid-cols-[32px_minmax(0,1fr)_minmax(0,240px)_auto]',
]) }}>
    <div class="hidden h-10 items-center justify-center md:flex">
        <span class="text-[15px] text-text-secondary tabular-nums group-hover:hidden">{{ $index }}</span>
        <button type="button" aria-label="Play {{ $song->title }}" class="hidden text-white group-hover:block">
            <x-lucide-play class="size-3.5 fill-current" />
        </button>
    </div>

    <div class="flex min-w-0 items-center gap-3">
        <x-cover :src="$song->cover_url" :art="$song->artwork" :seed="$song->album->title ?? $song->title" class="size-10 rounded" />
        <div class="flex min-w-0 flex-col gap-1">
            <a href="#" class="truncate text-[15px] leading-[18px] text-white hover:underline">{{ $song->title }}</a>
            <a href="#" class="truncate text-[13px] leading-4 text-text-secondary group-hover:text-white hover:underline">{{ $song->artist->name }}</a>
        </div>
    </div>

    <a href="#" class="hidden truncate text-sm text-text-secondary group-hover:text-white hover:underline md:block">{{ $song->album->title }}</a>

    <button type="button" aria-label="{{ $action }} {{ $song->title }} to this playlist"
        class="h-8 rounded-lg border border-outline px-4 text-[13px] font-bold text-white transition hover:scale-105 hover:border-white">
        {{ $action }}
    </button>
</div>
