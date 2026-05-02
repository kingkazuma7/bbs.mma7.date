<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FighterController;
use App\Http\Controllers\VoteController;
use Illuminate\Support\Facades\Route;

// 投票ページ
Route::get('/', [VoteController::class, 'index']);
Route::get('/people/vote/{fighterName}', [VoteController::class, 'vote'])->name('people.vote');
Route::get('/people/result/{fighterName}', [VoteController::class, 'result'])->name('people.result');
Route::get('/fighter/{id}', [VoteController::class, 'redirectLegacyFighterShow'])->whereNumber('id');

// 検索
Route::get('/search', [VoteController::class, 'search']);

// API ルート (AJAX)
Route::post('/api/votes', [VoteController::class, 'store']);
Route::post('/api/comments', [VoteController::class, 'storeComment']);
Route::post('/api/comment-reactions/{comment}', [VoteController::class, 'storeReaction']);

// 認証ルート
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 管理者用ルート（選手管理）
Route::middleware(['auth', 'admin.only'])->group(function () {
    Route::get('/admin/fighters', [FighterController::class, 'index']);
    Route::get('/admin/fighter/create', [FighterController::class, 'create']);
    Route::post('/admin/fighter', [FighterController::class, 'store']);
    Route::get('/admin/fighters/{id}/edit', [FighterController::class, 'edit']);
    Route::put('/admin/fighters/{id}', [FighterController::class, 'update']);
    Route::delete('/admin/fighters/{id}', [FighterController::class, 'destroy']);
});
