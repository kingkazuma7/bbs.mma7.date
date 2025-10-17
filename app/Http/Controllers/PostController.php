<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Thread;

class PostController extends Controller
{
    public function store(Request $request, Thread $thread)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'name' => 'nullable|string|max:255',
        ]);

        $thread->posts()->create($validated);

        return back()->with('success', '投稿が完了しました！');
    }
}
