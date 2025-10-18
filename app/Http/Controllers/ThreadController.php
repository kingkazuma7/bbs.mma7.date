<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Thread;
use App\Models\Event; // 追加
use App\Models\Fighter; // 追加

class ThreadController extends Controller
{
    public function index()
    {
        $threads = Thread::all(); // 全てのThreadを取得
        return view('threads.index', compact('threads'));
    }

    public function create()
    {
        $events = Event::all();
        $fighters = Fighter::all();
        return view('threads.create', compact('events', 'fighters'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'event_id' => 'nullable|exists:events,id',
            'fighter_id' => 'nullable|exists:fighters,id',
        ]);

        Thread::create($validated);

        return redirect()->route('threads.index')->with('success', 'スレッドが作成されました！');
    }

    public function show(Thread $thread)
    {
        return view('threads.show', compact('thread'));
    }
}
