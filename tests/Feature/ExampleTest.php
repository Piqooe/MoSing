<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_liked_songs_page_renders_the_saved_tracks(): void
    {
        $response = $this->get(route('liked-songs'));

        $response->assertOk()
            ->assertSee('Liked Songs')
            ->assertSee('Starlight Avenue')
            ->assertSee('data-song-like', false);
    }
}
