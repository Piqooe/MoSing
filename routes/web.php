<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PlaylistPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/liked-songs', [HomeController::class, 'likedSongs'])->name('liked-songs');
Route::get('/ui-kit', [HomeController::class, 'uiKit'])->name('ui-kit');

Route::view('/search', 'search')->name('search');
Route::get('/playlists/{slug}', [PlaylistPageController::class, 'show'])->name('playlists.show');