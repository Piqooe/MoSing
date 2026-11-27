<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Artist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    public function toggleArtist(Request $request, Artist $artist): JsonResponse|RedirectResponse
    {
        $changes = auth()->user()->followedArtists()->toggle($artist->id);

        return $request->expectsJson()
            ? response()->json(['following' => count($changes['attached']) > 0])
            : back();
    }

    public function toggleAlbum(Request $request, Album $album): JsonResponse|RedirectResponse
    {
        $changes = auth()->user()->savedAlbums()->toggle($album->id);

        return $request->expectsJson()
            ? response()->json(['saved' => count($changes['attached']) > 0])
            : back();
    }
}
