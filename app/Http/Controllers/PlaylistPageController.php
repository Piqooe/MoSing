<?php

namespace App\Http\Controllers;

use App\Support\DummyMusic;
use Illuminate\View\View;

class PlaylistPageController extends Controller
{
    public function show(string $slug): View
    {
        $playlist = DummyMusic::playlists()->get($slug);

        abort_if($playlist === null, 404);

        $songs = collect($playlist->songs)->sortBy('pivot.position')->values();

        $recommended = DummyMusic::songs()
            ->reject(fn ($song) => $songs->contains('id', $song->id))
            ->sortByDesc('plays')
            ->take($songs->isEmpty() ? 4 : 3)
            ->values();

        return view('playlist', [
            'playlist' => $playlist,
            'songs' => $songs,
            'totalDuration' => $songs->sum('duration'),
            'recommended' => $recommended,
        ]);
    }
}
