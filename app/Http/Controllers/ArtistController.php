<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use Illuminate\View\View;

class ArtistController extends Controller
{
    public function show(Artist $artist): View
    {
        $artist->load(['albums', 'songs.album']);

        $isFollowing = auth()->check()
            && auth()->user()->followedArtists()->whereKey($artist->id)->exists();

        return view('artist', [
            'artist' => $artist,
            'albums' => $artist->albums,
            'songs' => $artist->songs->sortByDesc('plays')->take(5)->values(),
            'isFollowing' => $isFollowing,
        ]);
    }
}
