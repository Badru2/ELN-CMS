<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::controller(PostController::class)->prefix("posts")->group(function () {
    Route::get('/', 'index')->name('posts.index');
    Route::get('/{id}', 'show')->name('posts.show');
    Route::post('/', 'store')->name('posts.store');
    Route::get('/{id}/edit', 'edit')->name('posts.edit');
    Route::put('/{id}', 'update')->name('posts.update');
    Route::delete('/{id}', 'destroy')->name('posts.destroy');
    Route::get('/{id}/restore', 'restore')->name('posts.restore');
    Route::get('/{id}/publish', 'publish')->name('posts.publish');
});

require __DIR__ . '/auth.php';
