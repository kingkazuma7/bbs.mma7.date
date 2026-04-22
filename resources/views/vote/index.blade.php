@extends('layouts.app')

@section('content')
<div class="container text-center">
    <h1 class="mb-4">この格闘家は強い？弱い？</h1>

    @if (!$currentFighter)
        <div class="alert alert-warning" role="alert">
            現在、投票できる格闘家がいません。管理者が格闘家を追加するまでお待ちください。
        </div>
    @else
        @php $fighter = $currentFighter; @endphp
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="mb-3">{{ $fighter->name }}</h2>
                @if ($fighter->image_url)
                    <img src="{{ asset('storage/' . $fighter->image_url) }}" class="img-fluid rounded mb-3" alt="{{ $fighter->name }}" style="max-height: 400px; object-fit: contain; object-position: top;">
                @else
                    <div class="bg-light d-flex align-items-center justify-content-center" style="width: 100%; height: 400px; border-radius: .25rem;">
                        <span class="text-muted">画像なし</span>
                    </div>
                @endif
                <div class="d-flex justify-content-center mt-4" id="vote-buttons-section">
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
                            <p><strong>合計投票数:</strong> <span id="total-count">0</span> 票</p>
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
                                            <li class="list-group-item py-3">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <small class="text-muted">
                                                        <strong>{{ $comment->id }}. 匿名@{{ $comment->vote_label }}</strong>
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
                                            {{ $comments->appends(['id' => $currentFighter->id, 'show_bbs' => '1'])->links() }}
                                        </div>
                                    @endif
                                @else
                                    <p class="text-muted">まだコメントはありません。</p>
                                @endif
                            </div>
                        </div>

                        <!-- コメント投稿フォーム -->
                        <div class="card">
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

                <div class="mt-4">
                    <button class="btn btn-primary btn-lg" onclick="window.location.reload();">次へ (新しい格闘家)</button>
                </div>
            </div>
        </div>

        <div class="mt-5 pt-4 border-top">
            <h3 class="mb-4">関連タグ</h3>
            <div class="row g-3">
                @foreach ($allFighters as $relatedFighter)
                    <div class="col-md-4 col-lg-3">
                        <a href="{{ url('/') }}?id={{ $relatedFighter->id }}" class="text-decoration-none text-dark">
                            <div class="card h-100 hover-shadow" style="transition: box-shadow 0.3s;">
                                @if ($relatedFighter->image_url)
                                    <img src="{{ asset('storage/' . $relatedFighter->image_url) }}" class="card-img-top" alt="{{ $relatedFighter->name }}" style="height: 200px; object-fit: contain; object-position: top;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px; border-bottom: 1px solid #dee2e6;">
                                        <span class="text-muted">画像なし</span>
                                    </div>
                                @endif
                                <div class="card-body text-center">
                                    <p class="card-text fw-bold">{{ $relatedFighter->name }}</p>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
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

        // スクロールしてコメントセクションを表示
        const bbsSection = document.getElementById('bbs-section');
        if (bbsSection) {
            bbsSection.scrollIntoView({ behavior: 'smooth' });
        }
    }

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
                    location.href = `/?id=${fighterId}&show_bbs=1`;
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