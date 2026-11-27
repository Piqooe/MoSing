<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Artist;
use Illuminate\Http\RedirectResponse;

class LibraryController extends Controller
{
    public function toggleArtist(Artist $artist): RedirectResponse
    {
        auth()->user()->followedArtists()->toggle($artist->id);

        return back();
    }

    public function toggleAlbum(Album $album): RedirectResponse
    {
        auth()->user()->savedAlbums()->toggle($album->id);

        return back();
    }
}
