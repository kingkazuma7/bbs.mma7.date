<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VoteController;

// 投票ページ
Route::get('/', [VoteController::class, 'index']);
Route::get('/fighter/{id}', [VoteController::class, 'show']);

// 検索
Route::get('/search', [VoteController::class, 'search']);

// API ルート (AJAX)
Route::post('/api/votes', [VoteController::class, 'store']);
Route::post('/api/comments', [VoteController::class, 'storeComment']);
Route::post('/api/comment-reactions/{comment}', [VoteController::class, 'storeReaction']);

