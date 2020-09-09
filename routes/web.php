<?php

use App\Http\Controllers\AjaxController;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\TaleController;
use Illuminate\Support\Facades\Route;

Auth::routes([
    'register' => false,
    'reset' => false,
    'confirm' => false,
    'verify' => false,
]);

Route::redirect('/', '/bajki')->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/bajki/create', [TaleController::class, 'create'])->name('tales.create');
    Route::post('/bajki', [TaleController::class, 'store'])->name('tales.store');
    Route::get('/bajki/{tale}/edit', [TaleController::class, 'edit'])->name('tales.edit');
    Route::match(['put', 'patch'], '/bajki/{tale}', [TaleController::class, 'update'])->name('tales.update');
    // Route::delete('/bajki/{tale}', [TaleController::class, 'destroy'])->name('tales.destroy');

    Route::get('/artysci/{artist}/edit', [ArtistController::class, 'edit'])->name('artists.edit');
    Route::match(['put', 'patch'], '/artysci/{artist}', [ArtistController::class, 'update'])->name('artists.update');
    Route::post('/artysci/{artist}/flush-cache', [ArtistController::class, 'flushCache'])->name('artists.flushCache');
    Route::delete('/artysci/{artist}', [ArtistController::class, 'destroy'])->name('artists.destroy');

    Route::get('/ajax/artists', [AjaxController::class, 'artists'])->name('ajax.artists');

    Route::get('/ajax/discogs', [AjaxController::class, 'discogs']);
    Route::get('/ajax/filmpolski', [AjaxController::class, 'filmPolski']);
    Route::get('/ajax/wikipedia', [AjaxController::class, 'wikipedia']);
});

Route::livewire('/bajki', 'tales')->name('tales.index');
Route::get('/bajki/{tale}', [TaleController::class, 'show'])->name('tales.show');

Route::livewire('/artysci', 'artists')->name('artists.index');
Route::get('/artysci/{artist}', [ArtistController::class, 'show'])->name('artists.show');
