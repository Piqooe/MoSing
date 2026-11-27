@extends('layouts.app')

@section('title', $album->title)

@section('content')
    <header class="flex flex-col gap-5 px-4 pt-8 pb-6 md:flex-row md:items-end md:px-6 md:pt-16">
        <x-cover :src="$album->cover_url" :art="$album->artwork" :seed="$album->title" class="size-44 rounded-lg shadow-xl md:size-56" />
        <div class="flex min-w-0 flex-col gap-2">
            <span class="text-xs font-semibold tracking-wider text-text-secondary uppercase">{{ ucfirst($album->type ?? 'Album') }}</span>
            <h1 class="truncate text-3xl font-bold md:text-5xl">{{ $album->title }}</h1>
            <p class="text-sm text-text-secondary">
                <a href="{{ route('artists.show', $album->artist) }}" class="font-semibold text-white hover:underline">{{ $album->artist->name }}</a>
                @if ($album->release_year) • {{ $album->release_year }} @endif
                • {{ $songs->count() }} songs
            </p>
            @if ($album->description)
                <p class="max-w-2xl text-sm text-text-secondary">{{ $album->description }}</p>
            @endif
        </div>
    </header>

    <div class="px-2 pb-8 md:px-6">
        <x-track-list-header class="mb-2" />
        @foreach ($songs as $song)
            <x-track-row :song="$song" :index="$song->track_number ?? $loop->iteration" />
        @endforeach
    </div>
@endsection
