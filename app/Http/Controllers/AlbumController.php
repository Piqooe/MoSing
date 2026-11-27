<?php

namespace App\Http\Controllers;

use App\Models\Album;
use Illuminate\View\View;

class AlbumController extends Controller
{
    public function show(Album $album): View
    {
        $album->load(['artist', 'songs.artist']);

        return view('album', [
            'album' => $album,
            'songs' => $album->songs,
        ]);
    }
}
