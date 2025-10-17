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

    public function show(Thread $thread)
    {
        return view('threads.show', compact('thread'));
    }
}
