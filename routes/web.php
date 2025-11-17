<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FighterController;
use App\Http\Controllers\ThreadController;
use App\Http\Controllers\PostController;

Route::redirect('/', '/threads');

// Eventリソースルート
//    Route::resource('events', EventController::class);

// Fighterリソースルート
Route::resource('fighters', FighterController::class);

// Fighterに関連するThread
Route::get('fighters/{fighter}/threads', [FighterController::class, 'threads'])->name('fighters.threads');

// Thread詳細とそれに紐付くPost
Route::resource('threads', ThreadController::class);
Route::resource('threads.posts', PostController::class)->only(['store']);
