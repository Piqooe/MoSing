@extends('layouts.app')

@section('title', $album->title)

@php
    $hasSongs = $songs->isNotEmpty();
    $iconBtn = 'flex items-center justify-center text-text-secondary transition hover:scale-105 hover:text-white';
    $rail = 'flex gap-3.5 overflow-x-auto snap-x snap-mandatory scroll-px-4 -mx-4 px-4 scrollbar-none *:snap-start
             md:mx-0 md:grid md:grid-cols-4 md:gap-3 md:overflow-visible md:px-0 xl:grid-cols-6
             md:max-xl:[&>*:nth-child(n+5)]:hidden';
@endphp

@section('content')
    <x-album-header :album="$album" :total-duration="$totalDuration" />

    <div class="bg-linear-to-b from-white/4 to-transparent px-4 pt-2 pb-3 md:px-6 md:py-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-5 md:gap-7">
                @if ($hasSongs)
                    <x-play-button size="xl" label="Play {{ $album->title }}" class="shadow-play" />
                    <button type="button" aria-label="Shuffle" aria-pressed="true" class="flex text-accent transition hover:scale-105">
                        <x-lucide-shuffle class="size-6 md:size-7" />
                    </button>
                @endif

                @auth
                    <form method="POST" action="{{ route('albums.save', $album) }}" class="flex" data-library-toggle>
                        @csrf
                        <button type="submit" data-variant="icon"
                            data-label-on="Remove from Your Library" data-label-off="Save to Your Library"
                            aria-label="{{ $isSaved ? 'Remove from' : 'Save to' }} Your Library"
                            aria-pressed="{{ $isSaved ? 'true' : 'false' }}"
                            @class([
                                'flex items-center justify-center transition hover:scale-105',
                                $isSaved ? 'text-accent' : 'text-text-secondary hover:text-white',
                            ])>
                            <x-lucide-heart @class(['size-6 md:size-[30px]', 'fill-current' => $isSaved]) />
                        </button>
                    </form>
                @endauth

                @if ($hasSongs)
                    <button type="button" aria-label="Download" class="{{ $iconBtn }}"><x-lucide-download class="size-[22px] md:size-[26px]" /></button>
                @endif
                <button type="button" aria-label="More options for {{ $album->title }}" class="{{ $iconBtn }}"><x-lucide-ellipsis class="size-[22px] md:size-7" /></button>
            </div>

            @if ($hasSongs)
                <button type="button" class="hidden items-center gap-2 text-sm font-semibold text-text-secondary transition-colors hover:text-white md:flex">
                    List <x-lucide-list class="size-[18px]" />
                </button>
            @endif
        </div>
    </div>

    @if ($hasSongs)
        <section class="px-2 md:px-6" aria-label="Tracklist">
            <x-track-list-header meta="Plays" :album="false" class="mb-2" />
            <div class="flex flex-col">
                @foreach ($songs as $song)
                    <x-track-row :song="$song" :index="$song->track_number ?? $loop->iteration"
                        :playing="$song->id === $nowPlaying?->id" :show-album="false" />
                @endforeach
            </div>

            @if ($album->release_year)
                <p class="mt-6 px-2 text-sm text-text-secondary md:px-0">Released {{ $album->release_year }}</p>
            @endif
        </section>
    @else
        <section class="px-4 pb-6 md:px-6 md:pb-10">
            <div class="hidden h-px bg-border md:block"></div>
            <x-empty-state icon="music" title="No songs yet" message="This album doesn't have any songs yet."
                class="mx-auto mt-6 max-w-[420px] md:mt-12" />
        </section>
    @endif

    @if ($moreByArtist->isNotEmpty())
        <section class="flex flex-col gap-4 px-4 pt-8 pb-10 md:px-6">
            <x-section-header :title="'More by '.$album->artist->name" :href="route('artists.show', $album->artist)" link-text="See discography" />
            <div class="{{ $rail }}">
                @foreach ($moreByArtist as $other)
                    <x-music-card :title="$other->title" :subtitle="collect([$other->release_year, ucfirst($other->type ?? 'album')])->filter()->implode(' • ')"
                        :cover="$other->cover_url" :art="$other->artwork" :href="route('albums.show', $other)" />
                @endforeach
            </div>
        </section>
    @endif
@endsection
