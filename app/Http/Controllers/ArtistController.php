<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use Illuminate\View\View;

class ArtistController extends Controller
{
    public function show(Artist $artist): View
    {
        $artist->load(['albums', 'songs.album']);

        return view('artist', [
            'artist' => $artist,
            'albums' => $artist->albums,
            'songs' => $artist->songs->sortByDesc('plays')->take(5)->values(),
        ]);
    }
}
