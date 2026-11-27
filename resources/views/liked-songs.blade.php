@extends('layouts.app')

@section('title', 'Liked Songs')

@php
    use App\Support\Format;
@endphp

@section('content')
    <header class="relative md:-mt-16 md:pt-16">
        <div class="absolute inset-0 -z-10 bg-linear-to-b from-[#4f46e5]/80 via-[#312e81]/55 to-transparent md:to-[#181818]/10"
            aria-hidden="true"></div>

        <div class="flex flex-col gap-4 px-4 pt-3 pb-5 md:flex-row md:items-end md:gap-7 md:px-6 md:pt-3 md:pb-7">
            <x-liked-tile class="mx-auto size-[220px] rounded shadow-[0_8px_24px_rgb(0_0_0/0.5)] md:mx-0 md:size-48 md:rounded-md lg:size-[232px]"
                icon="size-[72px] md:size-20" />

            <div class="flex min-w-0 flex-1 flex-col gap-2.5">
                <p class="hidden text-sm font-semibold md:block">Playlist</p>
                <h1 class="text-[32px] leading-tight font-bold tracking-[-1px] md:text-5xl md:tracking-[-1.4px] lg:text-6xl">Liked Songs</h1>
                <div class="flex items-center gap-2 text-sm">
                    <span class="flex size-6 items-center justify-center rounded-full bg-linear-to-br from-[#8B5CF6] to-[#1E1B4B] text-[10px] font-bold">
                        {{ Format::initials($user->name) }}
                    </span>
                    <span class="font-bold">{{ $user->name }}</span>
                    <span class="text-text-secondary" aria-hidden="true">•</span>
                    <span class="text-text-secondary"><span data-liked-count>{{ $likedCount }}</span> <span data-liked-count-label>{{ str('song')->plural($likedCount) }}</span></span>
                </div>
            </div>
        </div>
    </header>

    <div class="bg-linear-to-b from-white/4 to-transparent px-4 pt-2 pb-3 md:px-6 md:py-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-6 md:gap-7">
                <x-play-button size="xl" label="Play Liked Songs" class="size-[52px]! shadow-play" />
                <button type="button" aria-label="Shuffle Liked Songs" aria-pressed="true"
                    class="flex text-accent transition hover:scale-105">
                    <x-lucide-shuffle class="size-6 md:size-7" />
                </button>
                <button type="button" aria-label="Download Liked Songs"
                    class="flex items-center justify-center text-text-secondary transition hover:scale-105 hover:text-white">
                    <x-lucide-download class="size-[22px] md:size-[26px]" />
                </button>
                <button type="button" aria-label="More options for Liked Songs"
                    class="flex items-center justify-center text-text-secondary transition hover:scale-105 hover:text-white">
                    <x-lucide-ellipsis class="size-6 md:size-7" />
                </button>
            </div>

            <button type="button" class="hidden items-center gap-2 text-sm font-semibold text-text-secondary transition-colors hover:text-white md:flex">
                List <x-lucide-list class="size-[18px]" />
            </button>
        </div>
    </div>

    <section data-liked-songs-content class="px-2 pb-8 md:px-6" aria-label="Liked songs">
        <x-track-list-header class="mb-2" meta="Date added" />
        <div data-liked-song-list class="flex flex-col">
            @foreach ($songs as $song)
                <x-track-row :song="$song" :index="$loop->iteration" :meta="$song->added_at"
                    data-liked-song-row
                    :hidden="! $song->is_liked" />
            @endforeach
        </div>
    </section>

    <section data-liked-empty @if ($likedCount > 0) hidden @endif class="px-4 pb-10 md:px-6 md:pb-14">
        <div class="hidden h-px bg-border md:block"></div>
        <x-empty-state icon="heart" title="Songs you like will appear here"
            message="Save songs by tapping the heart, and find them all in one place."
            class="mx-auto mt-6 max-w-[460px] md:mt-12">
            <x-button variant="primary" icon="search" :href="route('search')">Find songs</x-button>
        </x-empty-state>
    </section>
@endsection
