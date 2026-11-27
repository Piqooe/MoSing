@props(['nowPlaying'])

@if ($nowPlaying)
    @include('partials.player', ['nowPlaying' => $nowPlaying])
    @include('partials.mini-player', ['nowPlaying' => $nowPlaying])
@endif
