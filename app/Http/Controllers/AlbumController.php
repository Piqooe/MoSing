<?php

namespace App\Http\Controllers;

use App\Models\Album;
use Illuminate\View\View;

class AlbumController extends Controller
{
    public function show(Album $album): View
    {
        $album->load(['artist', 'songs.artist']);

        $isSaved = auth()->check()
            && auth()->user()->savedAlbums()->whereKey($album->id)->exists();

        return view('album', [
            'album' => $album,
            'songs' => $album->songs,
            'isSaved' => $isSaved,
        ]);
    }
}
