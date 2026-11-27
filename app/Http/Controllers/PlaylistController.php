<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use App\Models\Song;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PlaylistController extends Controller
{
    /**
     * Menampilkan halaman detail playlist (kondisi berisi lagu maupun empty state).
     */
    public function show(Request $request, Playlist $playlist): View
    {
        $isOwner = auth()->check() && auth()->id() === $playlist->user_id;

        if (! $playlist->is_public && ! $isOwner) {
            abort(403, 'Playlist ini bersifat privat.');
        }

        // Muat lagu sesuai urutan posisi di tabel pivot playlist_song
        $playlist->load([
            'user',
            'songs' => fn ($query) => $query
                ->with(['artist', 'album'])
                ->orderBy('playlist_song.position'),
        ]);

        // Ambil daftar lagu yang belum ada di playlist untuk fitur "Tambah Lagu"
        $existingSongIds = $playlist->songs->pluck('id');
        $search = $request->query('q');

        $availableSongs = collect();
        if ($isOwner) {
            $availableSongs = Song::with(['artist', 'album'])
                ->whereNotIn('id', $existingSongIds)
                ->when($search, function ($query, $search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhereHas('artist', fn ($q) => $q->where('name', 'like', "%{$search}%"));
                })
                ->latest()
                ->take(10)
                ->get();
        }

        $songs = $playlist->songs;
        $totalDuration = $songs->sum('duration');
        $recommended = Song::with(['artist', 'album'])
            ->whereNotIn('id', $existingSongIds)
            ->inRandomOrder()
            ->take($songs->isEmpty() ? 4 : 3)
            ->get();

        return view('playlist', [
            'playlist'       => $playlist,
            'songs'          => $songs,
            'totalDuration'  => $totalDuration,
            'recommended'    => $recommended,
            'isOwner'        => $isOwner,
            'availableSongs' => $availableSongs,
            'search'         => $search,
        ]);
    }

    /**
     * Menyimpan playlist baru beserta upload cover opsional & default artwork.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_public'   => ['nullable', 'boolean'],
            'cover'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $coverPath = null;
        if ($request->hasFile('cover')) {
            $coverPath = $request->file('cover')->store('covers/playlists', 'public');
        }

        $user = $request->user();
        $slug = Str::slug($validated['title']) . '-' . Str::lower(Str::random(5));

        $playlist = Playlist::create([
            'user_id'     => $user->id,
            'owner_name'  => $user->name,
            'title'       => $validated['title'],
            'slug'        => $slug,
            'description' => $validated['description'] ?? null,
            'cover_url'   => $coverPath,
            'artwork'     => $this->generateArtwork($validated['title']),
            'is_public'   => $request->boolean('is_public', true),
        ]);

        return redirect()
            ->route('playlists.show', $playlist)
            ->with('success', 'Playlist berhasil dibuat!');
    }

    /**
     * Memperbarui judul, deskripsi, visibilitas, dan mengganti/menghapus cover lama.
     */
    public function update(Request $request, Playlist $playlist): RedirectResponse
    {
        abort_unless(auth()->id() === $playlist->user_id, 403);

        $validated = $request->validate([
            'title'        => ['required', 'string', 'max:120'],
            'description'  => ['nullable', 'string', 'max:500'],
            'is_public'    => ['nullable', 'boolean'],
            'cover'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_cover' => ['nullable', 'boolean'],
        ]);

        $coverPath = $playlist->cover_url;

        // Jika user mencentang hapus cover atau mengunggah cover baru
        if ($request->boolean('remove_cover') || $request->hasFile('cover')) {
            $this->deleteLocalCover($playlist->cover_url);
            $coverPath = null;
        }

        if ($request->hasFile('cover')) {
            $coverPath = $request->file('cover')->store('covers/playlists', 'public');
        }

        // Perbarui slug jika judul berubah
        $slug = $playlist->slug;
        if ($playlist->title !== $validated['title']) {
            $slug = Str::slug($validated['title']) . '-' . Str::lower(Str::random(5));
        }

        $playlist->update([
            'title'       => $validated['title'],
            'slug'        => $slug,
            'description' => $validated['description'] ?? null,
            'cover_url'   => $coverPath,
            'artwork'     => $playlist->artwork ?? $this->generateArtwork($validated['title']),
            'is_public'   => $request->boolean('is_public'),
        ]);

        return redirect()
            ->route('playlists.show', $playlist)
            ->with('success', 'Detail playlist diperbarui.');
    }

    /**
     * Menghapus playlist beserta file cover di storage dan relasi lagu di pivot.
     */
    public function destroy(Playlist $playlist): RedirectResponse
    {
        abort_unless(auth()->id() === $playlist->user_id, 403);

        $this->deleteLocalCover($playlist->cover_url);
        $playlist->songs()->detach();
        $playlist->delete();

        return redirect()
            ->route('home')
            ->with('success', 'Playlist berhasil dihapus.');
    }

    /**
     * Menambahkan lagu ke dalam playlist dengan posisi berurutan.
     */
    public function addSong(Request $request, Playlist $playlist): RedirectResponse
    {
        abort_unless(auth()->id() === $playlist->user_id, 403);

        $validated = $request->validate([
            'song_id' => ['required', 'integer', 'exists:songs,id'],
        ]);

        $songId = (int) $validated['song_id'];

        if ($playlist->songs()->where('songs.id', $songId)->exists()) {
            return back()->with('info', 'Lagu tersebut sudah ada di dalam playlist.');
        }

        $nextPosition = ((int) $playlist->songs()->max('playlist_song.position')) + 1;

        $playlist->songs()->attach($songId, [
            'position' => $nextPosition,
        ]);

        return back()->with('success', 'Lagu ditambahkan ke playlist.');
    }

    /**
     * Menghapus lagu dari playlist dan merapikan ulang urutan position.
     */
    public function removeSong(Playlist $playlist, Song $song): RedirectResponse
    {
        abort_unless(auth()->id() === $playlist->user_id, 403);

        $playlist->songs()->detach($song->id);

        // Rapikan ulang nomor urut position (1, 2, 3, ...)
        $remainingIds = $playlist->songs()
            ->orderBy('playlist_song.position')
            ->pluck('songs.id');

        foreach ($remainingIds as $index => $id) {
            $playlist->songs()->updateExistingPivot($id, [
                'position' => $index + 1,
            ]);
        }

        return back()->with('success', 'Lagu dihapus dari playlist.');
    }

    /**
     * Menghapus file cover lokal dari disk 'public' secara aman.
     */
    private function deleteLocalCover(?string $path): void
    {
        if (blank($path) || Str::startsWith($path, ['http://', 'https://'])) {
            return;
        }

        $cleanPath = preg_replace('#^/?storage/#', '', $path);

        if (Storage::disk('public')->exists($cleanPath)) {
            Storage::disk('public')->delete($cleanPath);
        }
    }

    /**
     * Menghasilkan array artwork gradien default saat playlist dibuat.
     */
    private function generateArtwork(string $seed): array
    {
        $palettes = [
            ['#1DB954', '#121212'],
            ['#6366F1', '#1E1B4B'],
            ['#EC4899', '#3B0764'],
            ['#F59E0B', '#451A03'],
            ['#06B6D4', '#083344'],
            ['#8B5CF6', '#2E1065'],
        ];

        $pair = $palettes[abs(crc32($seed)) % count($palettes)];

        return [
            'colors' => $pair,
            'from'   => $pair[0],
            'to'     => $pair[1],
            'angle'  => 135,
        ];
    }
}