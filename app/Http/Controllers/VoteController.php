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
use RalphJSmit\Laravel\SEO\Support\SEOData;

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

        $seoData = new SEOData(
            title: 'この格闘家は強い？弱い？',
            description: '格闘家の「強い・弱い」を投票で決める匿名掲示板。みんなの本音をチェック！',
        );

        return view('vote.index', compact('currentFighter', 'allFighters', 'seoData'));
    }

    public function show($id)
    {
        $fighter = Fighter::with(['votes', 'comments'])->findOrFail($id);
        $stats = $fighter->getVoteStats();
        $comments = $fighter->comments()->latest()->limit(50)->get();

        $seoModel = $fighter;

        return view('vote.show', compact('fighter', 'stats', 'comments', 'seoModel'));
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

    public function search(Request $request)
    {
        $query = $request->input('q', '');
        $results = [];

        if (!empty($query)) {
            $results = Fighter::where('name', 'like', '%' . $query . '%')->get();
            $seoData = new SEOData(
                title: "「{$query}」の検索結果",
                description: "「{$query}」の検索結果。格闘家の投票結果をチェック！",
            );
        } else {
            $seoData = new SEOData(
                title: '検索',
                description: '格闘家を検索します。',
            );
        }

        return view('vote.search', compact('query', 'results', 'seoData'));
    }
}
