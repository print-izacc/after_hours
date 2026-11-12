<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\NoteController as AdminNoteController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/notes');

Route::get('/notes', [NoteController::class, 'index'])->name('notes.index');
Route::get('/notes/create', [NoteController::class, 'create'])->name('notes.create');
Route::post('/notes', [NoteController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('notes.store');
Route::post('/notes/{note}/edit', [NoteController::class, 'authorizeEdit'])
    ->middleware('throttle:10,1')
    ->name('notes.edit.authorize');
Route::get('/notes/{note}/edit', [NoteController::class, 'edit'])
    ->middleware('throttle:10,1')
    ->name('notes.edit');
Route::put('/notes/{note}', [NoteController::class, 'update'])
    ->middleware('throttle:10,1')
    ->name('notes.update');
Route::delete('/notes/{note}', [NoteController::class, 'destroy'])
    ->middleware('throttle:10,1')
    ->name('notes.destroy');

Route::get('/profiles', [ProfileController::class, 'index'])->name('profiles.index');
Route::post('/profiles', [ProfileController::class, 'store'])->name('profiles.store');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'create'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('login.store');

    Route::middleware(['auth', 'can:access-admin'])->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'destroy'])->name('logout');
        Route::get('/notes', [AdminNoteController::class, 'index'])->name('notes.index');
        Route::delete('/notes/{note}', [AdminNoteController::class, 'destroy'])->name('notes.destroy');
    });
});
