<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Thread;

class ThreadController extends Controller
{
    public function index()
    {
        $threads = Thread::all(); // 全てのThreadを取得
        return view('threads.index', compact('threads'));
    }

    public function create()
    {
        return view('threads.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        Thread::create($validated);

        return redirect()->route('threads.index')->with('success', 'スレッドが作成されました！');
    }

    public function show(Thread $thread)
    {
        return view('threads.show', compact('thread'));
    }
}
