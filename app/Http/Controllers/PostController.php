<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Thread;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function store(Request $request, Thread $thread)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'name' => 'nullable|string|max:255',
        ]);

        if (empty($validated['name'])) {
            $validated['name'] = '匿名-' . Str::random(8);
        }

        $thread->posts()->create($validated);

        return back()->with('success', __('messages.post_success'));
    }
}
