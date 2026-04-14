<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VoteController;

// 投票ページ
Route::get('/', [VoteController::class, 'index']);
Route::get('/fighter/{id}', [VoteController::class, 'show']);

// API ルート (AJAX)
Route::post('/fighters/{fighter}/vote', [App\Http\Controllers\VoteController::class, 'store']);
Route::post('/fighters/{fighter}/comments', [VoteController::class, 'addComment']);

