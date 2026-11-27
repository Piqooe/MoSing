<?php

namespace Tests\Feature;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Song;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlbumPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_album_page_shows_songs_and_more_by_artist(): void
    {
        $artist = Artist::create(['name' => 'Luna Arden', 'slug' => 'luna-arden']);
        $album = Album::create(['artist_id' => $artist->id, 'title' => 'Midnight Echoes', 'slug' => 'midnight-echoes', 'type' => 'album', 'release_year' => 2026]);
        Album::create(['artist_id' => $artist->id, 'title' => 'Silver Lining', 'slug' => 'silver-lining', 'type' => 'single', 'release_year' => 2025]);
        Song::create(['artist_id' => $artist->id, 'album_id' => $album->id, 'title' => 'Starlight Avenue', 'slug' => 'starlight-avenue', 'duration' => 222, 'plays' => 48201733, 'track_number' => 1]);

        $this->get(route('albums.show', $album))
            ->assertOk()
            ->assertSee('Midnight Echoes')
            ->assertSee('Starlight Avenue')
            ->assertSee('48,201,733')
            ->assertSee('More by Luna Arden')
            ->assertSee('Silver Lining');
    }
}
