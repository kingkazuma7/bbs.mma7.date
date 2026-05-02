@extends('layouts.app')

@section('content')
@if($isTopPage)
    <div class="container">
        <!-- トップページリードブロック -->
        <div class="row align-items-center mb-5 text-start">
            <div class="col-md-7 mb-4 mb-md-0">
                <h2 class="display-4 fw-bold mb-4">あの格闘家って<br>強い？弱い？</h2>
                <p class="h4 mb-3 text-secondary">みんなの「ホンネ」が集まる場所。</p>
                <p class="text-muted lead">忖度なし。匿名だから言える、格闘家へのリアルな評価をチェックしよう。</p>
            </div>
            <div class="col-10 col-md-5 mx-auto me-md-0">
                <div class="bg-light rounded shadow-sm d-flex align-items-center justify-content-center overflow-hidden">
                    <img src="{{ asset('storage/top_hero.jpg') }}" class="img-fluid w-100" alt="Gachikaku Hero">
                </div>
            </div>
        </div>

        </div>
    </div>
@else
    <div class="container text-center">
        <h1 class="h2 mb-4">この格闘家は強い？弱い？</h1>

    @if (!$currentFighter)
        <div class="alert alert-warning" role="alert">
            現在、投票できる格闘家がいません。管理者が格闘家を追加するまでお待ちください。
        </div>
    @else
        @php $fighter = $currentFighter; @endphp
        <div class="row justify-content-center">
            <article class="col-md-6" data-after-comment-url="{{ route('people.vote', ['fighterName' => $fighter->name]) }}?show_bbs=1">
                <h2 class="mb-1">{{ $fighter->name }}</h2>
                <div class="mb-3 d-flex flex-wrap gap-1 justify-content-center">
                    @if($fighter->fight_style && count($fighter->fight_style) > 0)
                        @foreach($fighter->fight_style as $style)
                            <span class="badge rounded-pill bg-light text-dark border fw-bold">{{ $style }}</span>
                        @endforeach
                    @endif
                    
                    @if($fighter->weight_class && count($fighter->weight_class) > 0)
                        @foreach($fighter->weight_class as $cat)
                            <span class="badge rounded-pill bg-light text-secondary border fw-normal">{{ $cat }}</span>
                        @endforeach
                    @endif
                </div>
                @if ($fighter->image_path)
                    <img src="{{ $fighter->image_path }}" class="img-fluid rounded mb-3" alt="{{ $fighter->name }}" style="max-height: 400px; object-fit: contain; object-position: top;">
                @else
                    <div class="bg-light d-flex align-items-center justify-content-center" style="width: 100%; height: 400px; border-radius: .25rem;">
                        <span class="text-muted">画像なし</span>
                    </div>
                @endif

                <div class="mt-4 mb-3">
                    <p class="mb-1">「強い！」か「弱い！」に投票して<br>みんなのコメントを見てみよう！</p>
                    <p class="text-muted small">※投票は1日1回まで</p>
                </div>

                <div class="d-flex justify-content-center mt-4" id="vote-buttons-section" data-result-url="{{ route('people.result', ['fighterName' => $fighter->name]) }}">
                    <button class="btn btn-success btn-lg mx-2 vote-button" data-fighter-id="{{ $fighter->id }}" data-vote-type="strong">強い！</button>
                    <button class="btn btn-danger btn-lg mx-2 vote-button" data-fighter-id="{{ $fighter->id }}" data-vote-type="weak">弱い！</button>
                </div>

                <div id="vote-result" class="mt-4" style="display: none;">
                    <div class="card">
                        <div class="card-header">投票結果</div>
                        <div class="card-body">
                            <p><strong>強い:</strong> <span id="strong-count">0</span> 票 (<span id="strong-percentage">0</span>%)</p>
                            <div class="progress mb-2">
                                <div id="strong-bar" class="progress-bar bg-success" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <p><strong>弱い:</strong> <span id="weak-count">0</span> 票 (<span id="weak-percentage">0</span>%)</p>
                            <div class="progress mb-4">
                                <div id="weak-bar" class="progress-bar bg-danger" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemax="100"></div>
                            </div>
                            <p class="mb-3"><strong>合計投票数:</strong> <span id="total-count">0</span> 票</p>
                            
                            <hr>
                            <div class="mt-3 text-center">
                                <p class="small text-muted mb-2">結果をシェアしよう！</p>
                                <div class="d-flex justify-content-center gap-2 flex-wrap">
                                    <a id="share-x" href="#" target="_blank" class="btn btn-sm text-white" style="background-color: #1DA1F2; border-color: #1DA1F2;">
                                        <i class="fab fa-twitter"></i> X
                                    </a>
                                    <a id="share-line" href="#" target="_blank" class="btn btn-success btn-sm" style="background-color: #06C755; border-color: #06C755;">
                                        <i class="fab fa-line"></i> LINE
                                    </a>
                                    <button id="copy-url-btn" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-link"></i> URLをコピー
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 掲示板セクション -->
                    <div id="bbs-section" class="mt-4">
                        <!-- コメント一覧 -->
                        <div class="card mb-4">
                            <div class="card-header">掲示板 ({{ $comments->total() ?? 0 }}件)</div>
                            <div class="card-body">
                                @if ($comments && $comments->count() > 0)
                                    <ul class="list-group list-group-flush">
                                        @foreach ($comments as $comment)
                                            <li class="list-group-item py-3 text-start">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <small class="text-muted">
                                                        <strong>#{{ $comment->hash_id }} 匿名</strong>
                                                        {{ $comment->created_at->format('m-d H:i') }}
                                                    </small>
                                                    <small>
                                                        <a href="#" class="text-muted text-decoration-none">[通報]</a>
                                                        <a href="#" class="text-muted text-decoration-none">[非表示]</a>
                                                        <a href="#" class="text-muted text-decoration-none">[返信]</a>
                                                    </small>
                                                </div>
                                                <p class="mb-2">{{ $comment->content }}</p>
                                                <div>
                                                    <button class="btn btn-sm btn-outline-secondary good-btn" data-comment-id="{{ $comment->id }}">
                                                        <i class="fas fa-thumbs-up"></i> <span class="good-count">{{ $comment->good_count ?? 0 }}</span>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-secondary bad-btn ms-2" data-comment-id="{{ $comment->id }}">
                                                        <i class="fas fa-thumbs-down"></i> <span class="bad-count">{{ $comment->bad_count ?? 0 }}</span>
                                                    </button>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>

                                    <!-- ページネーション -->
                                        @if ($comments->hasPages())
                                        <div class="mt-3">
                                            {{ $comments->withQueryString()->links() }}
                                        </div>
                                    @endif
                                @else
                                    <p class="text-muted">まだコメントはありません。</p>
                                @endif
                            </div>
                        </div>

                        <!-- コメント投稿フォーム -->
                        <div class="card text-start">
                            <div class="card-header">コメントを投稿する</div>
                            <div class="card-body">
                                <form id="comment-form">
                                    <div class="mb-3">
                                        <select class="form-select" id="vote-type" name="vote_type" required>
                                            <option value="strong">強い派</option>
                                            <option value="weak">弱い派</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <textarea class="form-control" id="comment-content" name="content" rows="3" placeholder="匿名でコメントを書く" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">投稿</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </article>
        </div>

        <div class="mt-5 pt-4 border-top">
            <h3 class="mb-4">関連タグ</h3>
            <div class="row g-2 g-md-3">
                @foreach ($allFighters as $relatedFighter)
                    <div class="col-4 col-sm-4 col-md-3 col-lg-2">
                        <a href="{{ route('people.vote', ['fighterName' => $relatedFighter->name]) }}" class="text-decoration-none text-dark">
                            <div class="card h-100 hover-shadow" style="transition: box-shadow 0.3s;">
                                @if ($relatedFighter->image_path)
                                    <img src="{{ $relatedFighter->image_path }}" class="card-img-top" alt="{{ $relatedFighter->name }}" style="height: 100px; object-fit: contain; object-position: top;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 100px; border-bottom: 1px solid #dee2e6;">
                                        <small class="text-muted">画像なし</small>
                                    </div>
                                @endif
                                <div class="card-body p-2 text-center">
                                    <p class="card-text fw-bold small mb-0 text-truncate">{{ $relatedFighter->name }}</p>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
        </div>
    @endif
@endif

<!-- 共通：話題の格闘家（ハッシュタグ形式） -->
<div class="container mb-5">
    <div class="mt-5 pt-4 @if(!$isTopPage) border-top @endif">
        <h3 class="h5 mb-4 fw-bold text-secondary text-center text-md-start"><i class="fas fa-fire text-danger me-2"></i>話題の格闘家をチェック</h3>
        <div class="d-flex flex-wrap gap-2 justify-content-center justify-content-md-start">
            @foreach($topFighters as $topFighter)
                <a href="{{ route('people.vote', ['fighterName' => $topFighter->name]) }}" class="btn btn-light border rounded-pill px-3 px-md-4 py-2 hover-shadow transition-all" style="font-weight: 600; font-size: 0.9rem;">
                    <span class="text-primary me-1">#</span>{{ $topFighter->name }}
                </a>
            @endforeach
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const voteButtons = document.querySelectorAll('.vote-button');
    const fighterId = voteButtons.length > 0 ? voteButtons[0].dataset.fighterId : null;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    voteButtons.forEach(button => {
        button.addEventListener('click', function () {
            const fighterId = this.dataset.fighterId;
            const voteType = this.dataset.voteType;

            fetch('/api/votes', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    fighter_id: fighterId,
                    vote_type: voteType
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const section = document.getElementById('vote-buttons-section');
                    const resultUrl = section && section.dataset.resultUrl;
                    if (resultUrl) {
                        window.location.href = resultUrl;
                        return;
                    }
                    const stats = data.data.stats;
                    displayVoteResult(stats);
                } else {
                    alert(data.message);
                    // 投票失敗時も掲示板を表示
                    document.getElementById('vote-result').style.display = 'block';
                    document.getElementById('vote-buttons-section').style.display = 'none';
                    const bbsSection = document.getElementById('bbs-section');
                    if (bbsSection) {
                        bbsSection.scrollIntoView({ behavior: 'smooth' });
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('投票処理中にエラーが発生しました。');
            });
        });
    });

    function displayVoteResult(stats) {
        document.getElementById('strong-count').textContent = stats.strong_count;
        document.getElementById('strong-percentage').textContent = stats.strong_percentage;
        document.getElementById('strong-bar').style.width = stats.strong_percentage + '%';
        document.getElementById('strong-bar').setAttribute('aria-valuenow', stats.strong_percentage);

        document.getElementById('weak-count').textContent = stats.weak_count;
        document.getElementById('weak-percentage').textContent = stats.weak_percentage;
        document.getElementById('weak-bar').style.width = stats.weak_percentage + '%';
        document.getElementById('weak-bar').setAttribute('aria-valuenow', stats.weak_percentage);

        document.getElementById('total-count').textContent = stats.total_count;

        document.getElementById('vote-result').style.display = 'block';
        document.getElementById('vote-buttons-section').style.display = 'none';

        // シェアボタンの更新
        updateShareLinks();

        // スクロールしてコメントセクションを表示
        const bbsSection = document.getElementById('bbs-section');
        if (bbsSection) {
            bbsSection.scrollIntoView({ behavior: 'smooth' });
        }
    }

    function updateShareLinks() {
        const url = window.location.href;
        const fighterName = document.querySelector('h2') ? document.querySelector('h2').textContent : '';
        const text = `${fighterName} は強い？弱い？ 投票結果をチェック！ #格闘家強い弱い`;
        
        const xShare = document.getElementById('share-x');
        if (xShare) {
            xShare.href = `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(text)}`;
        }
        
        const lineShare = document.getElementById('share-line');
        if (lineShare) {
            lineShare.href = `https://social-plugins.line.me/lineit/share?url=${encodeURIComponent(url)}`;
        }
    }

    // URLコピーボタン
    const copyBtn = document.getElementById('copy-url-btn');
    if (copyBtn) {
        copyBtn.addEventListener('click', function() {
            navigator.clipboard.writeText(window.location.href).then(() => {
                const originalContent = this.innerHTML;
                this.innerHTML = '<i class="fas fa-check"></i> コピー完了';
                setTimeout(() => {
                    this.innerHTML = originalContent;
                }, 2000);
            });
        });
    }

    // 初期ロード時にもシェアリンクをセット
    updateShareLinks();

    // コメント投稿フォーム処理
    const commentForm = document.getElementById('comment-form');
    if (commentForm) {
        commentForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const content = document.getElementById('comment-content').value;
            const voteType = document.getElementById('vote-type').value;

            fetch('/api/comments', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    fighter_id: fighterId,
                    content: content,
                    vote_type: voteType
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    const afterCommentEl = document.querySelector('[data-after-comment-url]');
                    location.href = afterCommentEl ? afterCommentEl.dataset.afterCommentUrl : '/';
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('コメント投稿中にエラーが発生しました。');
            });
        });
    }

    // good/bad ボタン処理
    document.querySelectorAll('.good-btn, .bad-btn').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();

            const commentId = this.dataset.commentId;
            const type = this.classList.contains('good-btn') ? 'good' : 'bad';

            fetch(`/api/comment-reactions/${commentId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    type: type
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // カウントを更新
                    const button = this;
                    if (type === 'good') {
                        button.querySelector('.good-count').textContent = data.data.good_count;
                    } else {
                        button.querySelector('.bad-count').textContent = data.data.bad_count;
                    }
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('リアクション記録中にエラーが発生しました。');
            });
        });
    });

    // show_bbs パラメータがある場合、投票結果を最初から表示
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('show_bbs')) {
        const voteResult = document.getElementById('vote-result');
        if (voteResult) {
            voteResult.style.display = 'block';
            document.getElementById('vote-buttons-section').style.display = 'none';
        }
    }
});
</script>
@endsection