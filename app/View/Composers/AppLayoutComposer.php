<?php

namespace App\View\Composers;

use App\Models\Playlist;
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
        $liked = (object) [
            'type' => 'liked',
            'slug' => null,
            'title' => 'Liked Songs',
            'subtitle' => 'Playlist • '.$this->likedCount().' songs',
            'cover_url' => null,
            'artwork' => null,
            'is_playing' => false,
        ];

        $playlists = Playlist::with('user')
            ->where('is_public', true)
            ->orderBy('id')
            ->get()
            ->map(fn (Playlist $playlist) => (object) [
                'type' => 'playlist',
                'slug' => $playlist->slug,
                'title' => $playlist->title,
                'subtitle' => 'Playlist • '.$playlist->owner->name,
                'cover_url' => $playlist->cover_url,
                'artwork' => (array) $playlist->artwork,
                'is_playing' => false,
            ]);

        return collect([$liked])->concat($playlists)->values();
    }

    private function likedCount(): int
    {
        $user = auth()->user();

        return $user ? $user->likedSongs()->count() : 0;
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
