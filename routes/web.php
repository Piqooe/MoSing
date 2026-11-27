<?php

use App\Http\Controllers\AlbumController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\SearchPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/liked-songs', [HomeController::class, 'likedSongs'])->name('liked-songs');
Route::get('/ui-kit', [HomeController::class, 'uiKit'])->name('ui-kit');
Route::get('/search', [SearchPageController::class, 'index'])->name('search');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Halaman Detail Album & Artis
Route::get('/albums/{album:slug}', [AlbumController::class, 'show'])->name('albums.show');
Route::get('/artists/{artist:slug}', [ArtistController::class, 'show'])->name('artists.show');

// Halaman Detail Playlist (bisa diakses publik jika is_public = true)
Route::get('/playlists/{playlist:slug}', [PlaylistController::class, 'show'])
    ->name('playlists.show');

// Aksi CRUD Playlist & Kelola Lagu (wajib login)
Route::middleware('auth')->group(function () {
    Route::post('/playlists', [PlaylistController::class, 'store'])
        ->name('playlists.store');

    Route::put('/playlists/{playlist:slug}', [PlaylistController::class, 'update'])
        ->name('playlists.update');

    Route::delete('/playlists/{playlist:slug}', [PlaylistController::class, 'destroy'])
        ->name('playlists.destroy');

    Route::post('/playlists/{playlist:slug}/songs', [PlaylistController::class, 'addSong'])
        ->name('playlists.songs.store');

    Route::delete('/playlists/{playlist:slug}/songs/{song}', [PlaylistController::class, 'removeSong'])
        ->name('playlists.songs.destroy');

    Route::post('/artists/{artist:slug}/follow', [LibraryController::class, 'toggleArtist'])
        ->name('artists.follow');

    Route::post('/albums/{album:slug}/save', [LibraryController::class, 'toggleAlbum'])
        ->name('albums.save');
});