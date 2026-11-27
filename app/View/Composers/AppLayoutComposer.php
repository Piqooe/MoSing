<?php

namespace App\View\Composers;

use App\Models\Song;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class AppLayoutComposer
{
    public function compose(View $view): void
    {
        $user = $this->resolveUser();

        $view->with([
            'currentUser' => $user,
            'library' => $this->library(),
            'nowPlaying' => $this->nowPlaying($user),
        ]);
    }

    private function resolveUser(): object
    {
        $user = auth()->user();

        if (! $user && Schema::hasTable('users')) {
            $user = User::query()->first();
        }

        return $user ?? (object) ['id' => null, 'name' => 'Tamu', 'avatar_url' => null];
    }

    private function library(): Collection
    {
        $user = auth()->user();

        $liked = (object) [
            'type' => 'liked',
            'slug' => null,
            'title' => 'Liked Songs',
            'subtitle' => 'Playlist • '.($user ? $user->likedSongs()->count() : 0).' songs',
            'cover_url' => null,
            'artwork' => null,
            'is_playing' => false,
        ];

        if (! $user) {
            return collect([$liked]);
        }

        $artists = $user->followedArtists()->orderBy('name')->get()
            ->map(fn ($artist) => (object) [
                'type' => 'artist',
                'slug' => $artist->slug,
                'title' => $artist->name,
                'subtitle' => 'Artist',
                'cover_url' => $artist->photo_url,
                'artwork' => (array) $artist->artwork,
                'is_playing' => false,
            ]);

        $albums = $user->savedAlbums()->with('artist')->orderBy('title')->get()
            ->map(fn ($album) => (object) [
                'type' => 'album',
                'slug' => $album->slug,
                'title' => $album->title,
                'subtitle' => 'Album • '.$album->artist->name,
                'cover_url' => $album->cover_url,
                'artwork' => (array) $album->artwork,
                'is_playing' => false,
            ]);

        return collect([$liked])->concat($artists)->concat($albums)->values();
    }

    private function nowPlaying(object $user): ?Song
    {
        $query = Song::with(['artist', 'album'])->whereHas('album');

        $song = ($user->id ?? null) && method_exists($user, 'likedSongs')
            ? (clone $query)->whereIn('songs.id', $user->likedSongs()->select('songs.id'))->first()
            : null;

        $song ??= (clone $query)->orderByDesc('plays')->first();

        if ($song) {
            $song->position = 0;
        }

        return $song;
    }
}
