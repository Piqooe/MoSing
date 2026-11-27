<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_and_register_pages_render(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('Log in');
        $this->get(route('register'))->assertOk()->assertSee('Buat akun');
    }

    public function test_user_can_log_in_with_correct_password(): void
    {
        $user = User::factory()->create(['password' => 'password123']);

        $this->post(route('login'), ['email' => $user->email, 'password' => 'password123'])
            ->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $user = User::factory()->create(['password' => 'password123']);

        $this->post(route('login'), ['email' => $user->email, 'password' => 'salah'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_user_can_register_and_is_logged_in(): void
    {
        $this->post(route('register'), [
            'name' => 'Budi',
            'email' => 'budi@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('home'));

        $this->assertDatabaseHas('users', ['email' => 'budi@example.test', 'name' => 'Budi']);
        $this->assertTrue(Auth::check());
        $this->assertTrue(password_verify('password123', User::where('email', 'budi@example.test')->value('password')));
    }

    public function test_register_requires_matching_password_confirmation(): void
    {
        $this->post(route('register'), [
            'name' => 'Budi',
            'email' => 'budi@example.test',
            'password' => 'password123',
            'password_confirmation' => 'beda',
        ])->assertSessionHasErrors('password');

        $this->assertDatabaseMissing('users', ['email' => 'budi@example.test']);
    }

    public function test_logged_in_user_is_redirected_away_from_login_page(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('login'))
            ->assertRedirect();
    }

    public function test_user_can_log_out(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post(route('logout'))->assertRedirect(route('home'));

        $this->assertGuest();
    }

    public function test_guest_writing_playlist_is_redirected_to_login(): void
    {
        $this->post('/playlists', ['title' => 'Coba'])
            ->assertRedirect(route('login'));
    }
}
