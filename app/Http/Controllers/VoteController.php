<?php

namespace App\Http\Controllers;

use App\Models\Fighter;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VoteController extends Controller
{
    // 既存のindexメソッドなどがあれば残す

    public function index()
    {
        $fighters = Fighter::all();
        return view('vote.index', compact('fighters'));
    }

    public function show($id)
    {
        $fighter = Fighter::with(['votes', 'comments'])->findOrFail($id);
        $comments = $fighter->comments()->latest()->limit(50)->get();

        return view('vote.show', compact('fighter', 'comments'));
    }


    public function store(Request $request, Fighter $fighter)
    {
        $ipAddress = $request->ip();

        // 24時間以内の重複投票をチェック
        $lastVote = Vote::where('fighter_id', $fighter->id)
                        ->where('ip_address', $ipAddress)
                        ->where('voted_at', '>=', Carbon::now()->subHours(24))
                        ->first();

        if ($lastVote) {
            return response()->json(['message' => '既に24時間以内にこのファイターに投票しています。'], 403);
        }

        try {
            DB::beginTransaction();

            Vote::create([
                'fighter_id' => $fighter->id,
                'ip_address' => $ipAddress,
                'voted_at' => Carbon::now(),
            ]);

            DB::commit();

            // 投票成功時は最新の投票数を返す
            return response()->json([
                'message' => '投票が成功しました。',
                'votes_count' => $fighter->votes()->count(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => '投票に失敗しました。', 'error' => $e->getMessage()], 500);
        }
    }
}
