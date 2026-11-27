@extends('layouts.app')

@section('title', $artist->name)

@section('content')
    <header class="flex flex-col items-center gap-5 px-4 pt-8 pb-6 text-center md:flex-row md:items-end md:px-6 md:pt-16 md:text-left">
        <x-cover :src="$artist->photo_url" :art="$artist->artwork" :seed="$artist->name" :artist="true" class="size-44 rounded-full shadow-xl md:size-56" />
        <div class="flex min-w-0 flex-col gap-2">
            <span class="text-xs font-semibold tracking-wider text-text-secondary uppercase">Artist</span>
            <h1 class="truncate text-3xl font-bold md:text-5xl">{{ $artist->name }}</h1>
        </div>
    </header>

    @if ($songs->isNotEmpty())
        <section class="px-2 pb-6 md:px-6">
            <h2 class="mb-3 px-2 text-lg font-bold md:px-0">Popular</h2>
            @foreach ($songs as $song)
                <x-track-row :song="$song" :index="$loop->iteration" />
            @endforeach
        </section>
    @endif

    @if ($albums->isNotEmpty())
        <section class="px-4 pb-8 md:px-6">
            <h2 class="mb-3 text-lg font-bold">Albums</h2>
            <div class="flex flex-wrap gap-4">
                @foreach ($albums as $album)
                    <x-music-card :title="$album->title" :subtitle="$album->release_year.' • '.$artist->name"
                        :cover="$album->cover_url" :art="$album->artwork" :href="route('albums.show', $album)" />
                @endforeach
            </div>
        </section>
    @endif
@endsection
