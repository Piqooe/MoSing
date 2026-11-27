@extends('layouts.app')

@section('title', $playlist->title)

@php
    use App\Support\Format;

    $hasSongs = $songs->isNotEmpty();
    $ownedByMe = ($playlist->owner->name ?? null) === $currentUser->name;
    $saved = (bool) ($playlist->is_saved ?? ! $ownedByMe);
    $iconBtn = 'flex items-center justify-center text-text-secondary transition hover:scale-105 hover:text-white';
    $likeBtn = 'flex items-center justify-center transition hover:scale-105 '.($saved ? 'text-accent' : 'text-text-secondary hover:text-white');
@endphp

@section('content')
    <x-playlist-header :playlist="$playlist" :total-duration="$totalDuration" />

    <div class="bg-linear-to-b from-white/4 to-transparent px-4 pt-2 pb-3 md:px-6 md:py-6">
        <div class="flex items-center justify-between md:hidden">
            <div class="flex items-center gap-[22px]">
                @unless ($ownedByMe)
                    <button type="button" aria-label="{{ $saved ? 'Remove from' : 'Save to' }} Your Library" aria-pressed="{{ $saved ? 'true' : 'false' }}"
                        class="{{ $likeBtn }}">
                        <x-lucide-heart @class(['size-6', 'fill-current' => $saved]) />
                    </button>
                @endunless
                @if ($hasSongs)
                    <button type="button" aria-label="Download" class="{{ $iconBtn }}"><x-lucide-download class="size-[22px]" /></button>
                @endif
                <button type="button" aria-label="More options for {{ $playlist->title }}" class="{{ $iconBtn }}"><x-lucide-ellipsis class="size-[22px]" /></button>
            </div>
            @if ($hasSongs)
                <div class="flex items-center gap-5">
                    <button type="button" aria-label="Shuffle" aria-pressed="true" class="flex text-accent transition hover:scale-105"><x-lucide-shuffle class="size-6" /></button>
                    <x-play-button size="xl" label="Play {{ $playlist->title }}" class="size-[52px]!" />
                </div>
            @endif
        </div>

        <div class="hidden items-center justify-between md:flex">
            <div class="flex items-center gap-7">
                @if ($hasSongs)
                    <x-play-button size="xl" label="Play {{ $playlist->title }}" class="shadow-play" />
                    <button type="button" aria-label="Shuffle" aria-pressed="true" class="flex text-accent transition hover:scale-105"><x-lucide-shuffle class="size-7" /></button>
                @endif
                @unless ($ownedByMe)
                    <button type="button" aria-label="{{ $saved ? 'Remove from' : 'Save to' }} Your Library" aria-pressed="{{ $saved ? 'true' : 'false' }}"
                        class="{{ $likeBtn }}">
                        <x-lucide-heart @class(['size-[30px]', 'fill-current' => $saved]) />
                    </button>
                @endunless
                @if ($hasSongs)
                    <button type="button" aria-label="Download" class="{{ $iconBtn }}"><x-lucide-download class="size-[26px]" /></button>
                @endif
                <button type="button" aria-label="More options for {{ $playlist->title }}" class="{{ $iconBtn }}"><x-lucide-ellipsis class="size-7" /></button>
            </div>

            @if ($hasSongs)
                <button type="button" class="flex items-center gap-2 text-sm font-semibold text-text-secondary transition-colors hover:text-white">
                    List <x-lucide-list class="size-[18px]" />
                </button>
            @endif
        </div>
    </div>

    @if ($hasSongs)
        <section class="px-2 md:px-6" aria-label="Tracklist">
            <x-track-list-header class="mb-2" />
            <div class="flex flex-col">
                @foreach ($songs as $song)
                    <x-track-row :song="$song" :index="$song->pivot->position ?? $loop->iteration"
                        :playing="$song->id === $nowPlaying?->id"
                        :meta="Format::dateAdded($song->pivot->created_at ?? null)" />
                @endforeach
            </div>
        </section>
    @else
        <section class="px-4 pb-6 md:px-6 md:pb-10">
            <div class="hidden h-px bg-border md:block"></div>
            <x-empty-state icon="music" title="This playlist is empty" message="Find songs you love and add them here."
                class="mx-auto mt-6 max-w-[420px] md:mt-12">
                <x-button variant="primary" icon="search" :href="route('search')">Find songs</x-button>
            </x-empty-state>
        </section>
    @endif

    @if ($recommended->isNotEmpty())
        <section class="flex flex-col gap-4 px-2 pt-6 pb-10 md:px-6">
            <x-section-header title="Recommended" href="#" link-text="Refresh" class="px-2 md:px-0"
                :eyebrow="$hasSongs ? 'Based on what\'s in this playlist' : 'Popular with listeners like you'" />
            <div class="flex flex-col">
                @foreach ($recommended as $song)
                    <x-track-suggestion :song="$song" :index="$loop->iteration" />
                @endforeach
            </div>
        </section>
    @endif
@endsection
