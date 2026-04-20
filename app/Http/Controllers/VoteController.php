<?php

namespace App\Http\Controllers;

use App\Models\Fighter;
use App\Services\FighterService;
use App\Services\VoteService;
use App\Services\CommentService;
use App\Http\Requests\StoreVoteRequest;
use App\Http\Requests\StoreCommentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VoteController extends Controller
{
    protected $fighterService;
    protected $voteService;
    protected $commentService;

    public function __construct(FighterService $fighterService, VoteService $voteService, CommentService $commentService)
    {
        $this->fighterService = $fighterService;
        $this->voteService = $voteService;
        $this->commentService = $commentService;
    }

    public function index(Request $request)
    {
        if ($request->has('id')) {
            $currentFighter = Fighter::find($request->id);
            if (!$currentFighter) {
                $currentFighter = $this->fighterService->getRandomFighters(1)->first();
            }
        } else {
            $currentFighter = $this->fighterService->getRandomFighters(1)->first();
        }

        $allFighters = Fighter::all();

        return view('vote.index', compact('currentFighter', 'allFighters'));
    }

    public function show($id)
    {
        $fighter = Fighter::with(['votes', 'comments'])->findOrFail($id);
        $stats = $fighter->getVoteStats();
        $comments = $fighter->comments()->latest()->limit(50)->get();

        return view('vote.show', compact('fighter', 'stats', 'comments'));
    }

    public function store(StoreVoteRequest $request)
    {
        try {
            $fighter = Fighter::findOrFail($request->fighter_id);
            $ipAddress = $request->ip();

            if (!$this->voteService->canVote($fighter, $ipAddress)) {
                return response()->json(['success' => false, 'message' => '短期間に同じ格闘家への投票はできません。'], 429);
            }

            $stats = $this->voteService->vote(
                $fighter,
                $request->vote_type,
                $ipAddress
            );

            return response()->json([
                'success' => true,
                'message' => '投票ありがとうございました',
                'data' => [
                    'fighter_id' => $fighter->id,
                    'vote_type' => $request->vote_type,
                    'stats' => $stats,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Vote store error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => '投票中にエラーが発生しました。'], 500);
        }
    }

    public function storeComment(StoreCommentRequest $request)
    {
        try {
            $fighter = Fighter::findOrFail($request->fighter_id);
            $ipAddress = $request->ip();

            if (!$this->commentService->canPostComment($ipAddress)) {
                return response()->json(['success' => false, 'message' => 'しばらく待ってからコメントしてください。'], 429);
            }

            if (!$this->commentService->validateContent($request->content)) {
                return response()->json(['success' => false, 'message' => 'コメント内容が不適切です。'], 400);
            }

            $comment = $this->commentService->postComment(
                $fighter->id,
                $request->content,
                $ipAddress
            );

            return response()->json([
                'success' => true,
                'message' => 'コメントありがとうございました',
                'data' => $comment,
            ]);
        } catch (\Exception $e) {
            Log::error('Comment store error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'コメント投稿中にエラーが発生しました。'], 500);
        }
    }
}
