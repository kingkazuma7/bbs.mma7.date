<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\StoreVoteRequest;
use App\Models\Comment;
use App\Models\Fighter;
use App\Services\CommentService;
use App\Services\FighterService;
use App\Services\VoteService;
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
        if ($request->filled('id')) {
            $fighter = Fighter::find($request->id);
            if ($fighter) {
                $url = route('people.vote', ['fighterName' => $fighter->name]);
                if ($request->has('show_bbs')) {
                    $url .= (str_contains($url, '?') ? '&' : '?').'show_bbs=1';
                }

                return redirect()->to($url, 301);
            }

            return redirect('/', 301);
        }

        $isTopPage = true;
        $currentFighter = null;
        $comments = collect();

        $topFighters = Fighter::withCount('votes')->orderBy('votes_count', 'desc')->take(15)->get();

        $allFighters = Fighter::inRandomOrder()->take(6)->get();
        $showBbs = false;

        $seoData = new SEOData(
            title: 'ガチ格｜格闘家「強い・弱い」みんなのホンネが集まる掲示板',
            description: '格闘家の「強い・弱い」を投票で決める匿名掲示板。みんなの本音をチェック！',
            image: asset('storage/top_hero.jpg'),
        );

        return view('vote.index', compact('currentFighter', 'allFighters', 'comments', 'showBbs', 'seoData', 'isTopPage', 'topFighters'));
    }

    public function vote(Request $request, string $fighterName)
    {
        $currentFighter = Fighter::where('name', $fighterName)->firstOrFail();
        $comments = $currentFighter->comments()->latest()->paginate(10);

        $topFighters = Fighter::withCount('votes')->orderBy('votes_count', 'desc')->take(15)->get();

        $allFighters = Fighter::inRandomOrder()->take(6)->get();
        $showBbs = $request->has('show_bbs');

        $isTopPage = false;

        $seoData = new SEOData(
            title: "{$currentFighter->name}は強い？弱い？｜ガチ格",
            description: "{$currentFighter->name}の「強い・弱い」投票結果とみんなのコメント。格闘家のリアルな評価をチェックしよう。",
            image: $currentFighter->image_path ?? asset('storage/top_hero.jpg'),
        );

        return view('vote.index', compact('currentFighter', 'allFighters', 'comments', 'showBbs', 'seoData', 'isTopPage', 'topFighters'));
    }

    public function result(string $fighterName)
    {
        $fighter = Fighter::with(['votes', 'comments'])->where('name', $fighterName)->firstOrFail();
        $stats = $fighter->getVoteStats();
        $comments = $fighter->comments()->latest()->limit(50)->get();

        $topFighters = Fighter::withCount('votes')->orderBy('votes_count', 'desc')->take(15)->get();
        $allFighters = Fighter::inRandomOrder()->take(6)->get();

        $seoModel = $fighter;

        return view('vote.show', compact('fighter', 'stats', 'comments', 'seoModel', 'topFighters', 'allFighters'));
    }

    public function redirectLegacyFighterShow(string $id)
    {
        $fighter = Fighter::findOrFail($id);

        return redirect()->route('people.result', ['fighterName' => $fighter->name], 301);
    }

    public function store(StoreVoteRequest $request)
    {
        try {
            $fighter = Fighter::findOrFail($request->fighter_id);
            $ipAddress = $request->ip();

            if (! $this->voteService->canVote($fighter, $ipAddress)) {
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
            Log::error('Vote store error: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => '投票中にエラーが発生しました。'], 500);
        }
    }

    public function storeComment(StoreCommentRequest $request)
    {
        try {
            $fighter = Fighter::findOrFail($request->fighter_id);
            $ipAddress = $request->ip();

            if (! $this->commentService->canPostComment($ipAddress)) {
                return response()->json(['success' => false, 'message' => 'しばらく待ってからコメントしてください。'], 429);
            }

            if (! $this->commentService->validateContent($request->content)) {
                return response()->json(['success' => false, 'message' => 'コメント内容が不適切です。'], 400);
            }

            $comment = $this->commentService->postComment(
                $fighter->id,
                $request->content,
                $ipAddress,
                $request->vote_type
            );

            return response()->json([
                'success' => true,
                'message' => 'コメントありがとうございました',
                'data' => $comment,
            ]);
        } catch (\Exception $e) {
            Log::error('Comment store error: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => 'コメント投稿中にエラーが発生しました。'], 500);
        }
    }

    public function storeReaction(Request $request, Comment $comment)
    {
        try {
            $type = $request->input('type');

            if (! in_array($type, ['good', 'bad'])) {
                return response()->json(['success' => false, 'message' => '無効なリアクションタイプです。'], 400);
            }

            if ($type === 'good') {
                $comment->increment('good_count');
            } else {
                $comment->increment('bad_count');
            }

            return response()->json([
                'success' => true,
                'message' => 'リアクションを記録しました',
                'data' => [
                    'good_count' => $comment->good_count,
                    'bad_count' => $comment->bad_count,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Comment reaction store error: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => 'リアクション記録中にエラーが発生しました。'], 500);
        }
    }

    public function search(Request $request)
    {
        $query = $request->input('q', '');
        $results = [];

        if (! empty($query)) {
            $results = Fighter::where('name', 'like', '%'.$query.'%')->get();
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
