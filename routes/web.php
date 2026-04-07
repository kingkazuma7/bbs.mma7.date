<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VoteController;

// 投票ページ
Route::get('/', [VoteController::class, 'index']);
Route::get('/fighter/{id}', [VoteController::class, 'show']);

// API ルート (AJAX)
Route::post('/api/votes', [VoteController::class, 'store']);
Route::post('/api/comments', [VoteController::class, 'storeComment']);

