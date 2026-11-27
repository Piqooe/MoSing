<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\Artist;
use App\Models\Playlist;
use App\Models\Song;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));

        if ($q === '') {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'songs' => [],
                    'albums' => [],
                    'artists' => [],
                    'playlists' => [],
                ],
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'songs' => Song::with(['artist', 'album'])
                    ->where('title', 'like', "%{$q}%")
                    ->take(10)
                    ->get(),
                'albums' => Album::with('artist')
                    ->where('title', 'like', "%{$q}%")
                    ->take(6)
                    ->get(),
                'artists' => Artist::where('name', 'like', "%{$q}%")
                    ->take(6)
                    ->get(),
                'playlists' => Playlist::withCount('songs')
                    ->where('title', 'like', "%{$q}%")
                    ->take(6)
                    ->get(),
            ],
        ]);
    }

    public function browse(): \Illuminate\Http\JsonResponse
    {
        $categories = \App\Models\Category::all();

        $trendingSongs = \App\Models\Song::with(['artist', 'album'])
            ->orderByDesc('plays_count')
            ->take(8)
            ->get();

        $featuredAlbums = \App\Models\Album::with('artist')
            ->latest()
            ->take(6)
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Browse data retrieved successfully.',
            'data' => [
                'categories' => $categories,
                'trending_songs' => $trendingSongs,
                'featured_albums' => $featuredAlbums,
            ],
        ]);
    }
    
    public function storeCategory(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'color' => 'nullable|string|max:100',
        ]);

        $category = \App\Models\Category::create([
            'name' => $validated['name'],
            'slug' => \Illuminate\Support\Str::slug($validated['name']),
            'color' => $validated['color'] ?? 'from-emerald-500 to-teal-600',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Category created successfully.',
            'data' => $category,
        ], 201);
    }
}