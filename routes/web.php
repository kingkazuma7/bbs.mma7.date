<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FighterController;
use App\Http\Controllers\ThreadController;
use App\Http\Controllers\PostController;

Route::get('/', function () {
    return view('welcome');
});

// Eventリソースルート
Route::resource('events', EventController::class);

// Fighterに関連するThread
Route::get('fighters/{fighter}/threads', [FighterController::class, 'threads'])->name('fighters.threads');

// Thread詳細とそれに紐付くPost
Route::resource('threads', ThreadController::class);
Route::resource('threads.posts', PostController::class)->only(['store']);
