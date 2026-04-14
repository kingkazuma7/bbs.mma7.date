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
        $comments = collect(); // デフォルトは空のコレクション

        if ($fighters->isNotEmpty()) {
            $fighter = $fighters->first();
            $comments = $fighter->comments()->latest()->limit(50)->get();
        }

        return view('vote.index', compact('fighters', 'comments'));
    }

    public function show($id)
    {
        $fighter = Fighter::with(['votes', 'comments'])->findOrFail($id);
        $comments = $fighter->comments()->latest()->limit(50)->get();

        return view('vote.show', compact('fighter', 'comments'));
    }


    public function store(Request $request, Fighter $fighter)
    {
        $request->validate([
            'vote_type' => ['required', 'in:strong,weak'],
        ]);

        $voteType = $request->input('vote_type');
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
                'type' => $voteType,
            ]);

            DB::commit();

            $totalVotes = $fighter->votes()->count();
            $strongVotes = $fighter->votes()->where('type', 'strong')->count();
            $weakVotes = $fighter->votes()->where('type', 'weak')->count();

            $strongPercentage = $totalVotes > 0 ? round(($strongVotes / $totalVotes) * 100, 2) : 0;
            $weakPercentage = $totalVotes > 0 ? round(($weakVotes / $totalVotes) * 100, 2) : 0;

            return response()->json([
                'message' => '投票が成功しました。',
                'total_votes' => $totalVotes,
                'strong_votes' => $strongVotes,
                'weak_votes' => $weakVotes,
                'strong_percentage' => $strongPercentage,
                'weak_percentage' => $weakPercentage,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => '投票に失敗しました。', 'error' => $e->getMessage()], 500);
        }
    }

    public function addComment(Request $request, Fighter $fighter)
    {
        $request->validate([
            'content' => 'required|string|max:500',
            'user_name' => 'nullable|string|max:50',
        ]);

        try {
            $comment = $fighter->comments()->create([
                'user_name' => $request->input('user_name'),
                'content' => $request->input('content'),
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'message' => 'コメントが投稿されました。',
                'comment' => $comment,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'コメントの投稿に失敗しました。', 'error' => $e->getMessage()], 500);
        }
    }
}
