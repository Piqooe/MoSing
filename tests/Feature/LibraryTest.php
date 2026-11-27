<?php

namespace Tests\Feature;

use App\Models\Album;
use App\Models\Artist;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LibraryTest extends TestCase
{
    use RefreshDatabase;

    private function artist(): Artist
    {
        return Artist::create(['name' => 'Luna Arden', 'slug' => 'luna-arden']);
    }

    private function album(Artist $artist): Album
    {
        return Album::create([
            'artist_id' => $artist->id,
            'title' => 'Midnight Echoes',
            'slug' => 'midnight-echoes',
            'type' => 'album',
            'release_year' => 2026,
        ]);
    }

    public function test_user_can_follow_and_unfollow_an_artist(): void
    {
        $user = User::factory()->create();
        $artist = $this->artist();

        $this->actingAs($user)->post(route('artists.follow', $artist));
        $this->assertDatabaseHas('artist_user', ['user_id' => $user->id, 'artist_id' => $artist->id]);

        $this->actingAs($user)->post(route('artists.follow', $artist));
        $this->assertDatabaseMissing('artist_user', ['user_id' => $user->id, 'artist_id' => $artist->id]);
    }

    public function test_user_can_save_and_unsave_an_album(): void
    {
        $user = User::factory()->create();
        $album = $this->album($this->artist());

        $this->actingAs($user)->post(route('albums.save', $album));
        $this->assertDatabaseHas('album_user', ['user_id' => $user->id, 'album_id' => $album->id]);

        $this->actingAs($user)->post(route('albums.save', $album));
        $this->assertDatabaseMissing('album_user', ['user_id' => $user->id, 'album_id' => $album->id]);
    }

    public function test_followed_artist_and_saved_album_appear_in_library(): void
    {
        $user = User::factory()->create();
        $artist = $this->artist();
        $album = $this->album($artist);

        $user->followedArtists()->attach($artist->id);
        $user->savedAlbums()->attach($album->id);

        $this->actingAs($user)->get('/')
            ->assertOk()
            ->assertSee('data-lib-type="artist"', false)
            ->assertSee('data-lib-type="album"', false)
            ->assertSee('Luna Arden')
            ->assertSee('Midnight Echoes');
    }

    public function test_artist_page_shows_following_state(): void
    {
        $user = User::factory()->create();
        $artist = $this->artist();
        $user->followedArtists()->attach($artist->id);

        $this->actingAs($user)->get(route('artists.show', $artist))
            ->assertOk()
            ->assertSee('Following');
    }
}
