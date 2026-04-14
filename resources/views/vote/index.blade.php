@extends('layouts.app')

@section('content')
<div class="container text-center">
    <h1 class="mb-4">この格闘家は強い？弱い？</h1>

    @if ($fighters->isEmpty())
        <div class="alert alert-warning" role="alert">
            現在、投票できる格闘家がいません。管理者が格闘家を追加するまでお待ちください。
        </div>
    @else
        @php $fighter = $fighters->first(); @endphp
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="mb-3">{{ $fighter->name }}</h2>
                <div id="votes-count-{{ $fighter->id }}" class="h4 mb-3">現在の票数: {{ $fighter->votes_count }}</div>
                <div id="vote-percentages-{{ $fighter->id }}" class="mb-3" style="display: none;">
                    <p>強い！: <span id="strong-percentage">0</span>%</p>
                    <p>弱い！: <span id="weak-percentage">0</span>%</p>
                </div>
                @if ($fighter->image_url)
                    <img src="{{ asset('storage/' . $fighter->image_url) }}" class="img-fluid rounded mb-3" alt="{{ $fighter->name }}" style="max-height: 400px; object-fit: cover;">
                @else
                    <div class="bg-light d-flex align-items-center justify-content-center" style="width: 100%; height: 400px; border-radius: .25rem;">
                        <span class="text-muted">画像なし</span>
                    </div>
                @endif
                <div class="d-flex justify-content-center mt-4">
                    <button class="btn btn-success btn-lg mx-2 vote-button" data-fighter-id="{{ $fighter->id }}" data-vote-type="strong">強い！</button>
                    <button class="btn btn-danger btn-lg mx-2 vote-button" data-fighter-id="{{ $fighter->id }}" data-vote-type="weak">弱い！</button>
                </div>
                <div class="mt-4">
                </div>
                <button id="copyUrlButton" class="btn btn-secondary btn-sm mt-3">URLをコピー</button>
            </div>
        </div>
    @endif
</div>

    <div class="mt-5">
        <h3>コメント掲示板</h3>
        <form id="comment-form" class="mb-4">
            <div class="mb-3">
                <label for="userName" class="form-label">名前 (任意)</label>
                <input type="text" class="form-control" id="userName" name="user_name" maxlength="50">
            </div>
            <div class="mb-3">
                <label for="commentContent" class="form-label">コメント</label>
                <textarea class="form-control" id="commentContent" name="content" rows="3" required maxlength="500"></textarea>
            </div>
            <button type="submit" class="btn btn-success">コメントを投稿</button>
        </form>

        <div id="comments-list" class="list-group">
            @forelse ($comments as $comment)
                <div class="list-group-item list-group-item-action flex-column align-items-start mb-2">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1">{{ $comment->user_name ?: "匿名" }}</h5>
                        <small>{{ $comment->created_at->diffForHumans() }}</small>
                    </div>
                    <p class="mb-1">{{ $comment->content }}</p>
                </div>
            @empty
                <p class="text-muted">まだコメントはありません。</p>
            @endforelse
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const voteButtons = document.querySelectorAll('.vote-button');
    voteButtons.forEach(button => {
        button.addEventListener('click', function () {
            const fighterId = this.dataset.fighterId;
            const voteType = this.dataset.voteType;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch(`/fighters/${fighterId}/vote`, {
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
                if (data.message) {
                    alert(data.message);
                }
                if (data.total_votes !== undefined) {
                    document.getElementById(`votes-count-${fighterId}`).innerText = `現在の総票数: ${data.total_votes}`;
                    document.getElementById(`strong-percentage`).innerText = data.strong_percentage;
                    document.getElementById(`weak-percentage`).innerText = data.weak_percentage;
                    document.getElementById(`vote-percentages-${fighterId}`).style.display = 'block';
                }
                // window.location.reload(); // 画面リロードは不要
            })
            .catch(error => {
                console.error('Error:', error);
                alert('投票処理中にエラーが発生しました。');
            });
        });
    });

    const copyUrlButton = document.getElementById('copyUrlButton');
    if (copyUrlButton) {
        copyUrlButton.addEventListener('click', function() {
            const url = window.location.href;
            navigator.clipboard.writeText(url).then(() => {
                alert('URLがクリップボードにコピーされました！');
            }).catch(err => {
                console.error('URLのコピーに失敗しました。', err);
                alert('URLのコピーに失敗しました。');
            });
        });
    }

    // コメント投稿フォームの処理
    const commentForm = document.getElementById('comment-form');
    if (commentForm) {
        commentForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const fighterId = {{ $fighter->id ?? 'null' }};
            if (fighterId === null) {
                alert('コメントを投稿するファイターがいません。');
                return;
            }

            const userName = document.getElementById('userName').value;
            const commentContent = document.getElementById('commentContent').value;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch(`/fighters/${fighterId}/comments`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    user_name: userName,
                    content: commentContent
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    alert(data.message);
                }
                if (data.comment) {
                    const commentsList = document.getElementById('comments-list');
                    const newCommentHtml = `
                        <div class="list-group-item list-group-item-action flex-column align-items-start mb-2">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">${data.comment.user_name || "匿名"}</h5>
                                <small>たった今</small>
                            </div>
                            <p class="mb-1">${data.comment.content}</p>
                        </div>
                    `;
                    commentsList.insertAdjacentHTML('afterbegin', newCommentHtml);
                    document.getElementById('commentContent').value = ''; // フォームをクリア
                    document.getElementById('userName').value = ''; // フォームをクリア
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('コメントの投稿に失敗しました。');
            });
        });
    }
});
</script>
@endsection