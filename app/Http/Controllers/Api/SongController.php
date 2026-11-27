<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Song;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SongController extends Controller
{
    public function show(Song $song): JsonResponse
    {
        $song->load(['artist', 'album']);

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $song->id,
                'title' => $song->title,
                'slug' => $song->slug,
                'audio_url' => $song->audio_url,
                'duration' => $song->duration,
                'plays' => $song->plays,
                'is_liked' => $song->is_liked,
                'cover_url' => $song->cover_url,
                'artwork' => $song->artwork,
                'artist' => $song->artist ? [
                    'id' => $song->artist->id,
                    'name' => $song->artist->name,
                    'slug' => $song->artist->slug,
                ] : null,
                'album' => $song->album ? [
                    'id' => $song->album->id,
                    'title' => $song->album->title,
                    'slug' => $song->album->slug,
                ] : null,
            ],
        ]);
    }

    public function play(Song $song): JsonResponse
    {
        $song->increment('plays');

        return response()->json([
            'status' => 'success',
            'plays' => $song->plays,
        ]);
    }

    public function toggleLike(Song $song): JsonResponse
    {
        $user = Auth::user() ?? User::first();
        $user->likedSongs()->toggle($song->id);

        $isLiked = $user->likedSongs()->where('song_id', $song->id)->exists();

        return response()->json([
            'status' => 'success',
            'is_liked' => $isLiked,
            'liked_count' => $user->likedSongs()->count(),
        ]);
    }

    public function liked(Request $request): \Illuminate\Http\JsonResponse
    {
        $likedSongs = $request->user()
            ->likedSongs()
            ->with(['artist', 'album'])
            ->latest('liked_songs.created_at')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Liked songs retrieved successfully.',
            'total_songs' => $likedSongs->count(),
            'total_duration' => $likedSongs->sum('duration'),
            'data' => $likedSongs,
        ]);
    }
}
