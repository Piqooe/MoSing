<?php

namespace App\Http\Controllers;

use App\Models\Album;
use Illuminate\View\View;

class AlbumController extends Controller
{
    public function show(Album $album): View
    {
        $album->load(['artist', 'songs.artist', 'songs.album']);

        $isSaved = auth()->check()
            && auth()->user()->savedAlbums()->whereKey($album->id)->exists();

        $moreByArtist = Album::where('artist_id', $album->artist_id)
            ->whereKeyNot($album->id)
            ->orderByDesc('release_year')
            ->take(6)
            ->get();

        return view('album', [
            'album' => $album,
            'songs' => $album->songs,
            'totalDuration' => $album->songs->sum('duration'),
            'isSaved' => $isSaved,
            'moreByArtist' => $moreByArtist,
        ]);
    }
}
