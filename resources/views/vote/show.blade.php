@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h1 class="mb-4">{{ $fighter->name }} の詳細</h1>

            @if ($fighter->image_url)
                <img src="{{ asset('storage/' . $fighter->image_url) }}" class="img-fluid rounded mb-4" alt="{{ $fighter->name }}" style="max-height: 400px; object-fit: cover;">
            @else
                <div class="bg-light d-flex align-items-center justify-content-center mb-4" style="width: 100%; height: 400px; border-radius: .25rem;">
                    <span class="text-muted">画像なし</span>
                </div>
            @endif

            <div class="card mb-4">
                <div class="card-header">投票統計</div>
                <div class="card-body">
                    <p><strong>合計投票数:</strong> <span id="total-votes-count">{{ $fighter->votes_count }}</span> 票</p>
                </div>
            </div>

            <div class="mb-4">
                <a href="{{ url('/') }}" class="btn btn-secondary">投票ページに戻る</a>
            </div>

            <div class="card mb-4">
                <div class="card-header">コメントを投稿</div>
                <div class="card-body">
                    <form id="comment-form">
                        <div class="mb-3">
                            <label for="content" class="form-label">コメント (匿名)</label>
                            <textarea class="form-control" id="content" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">投稿</button>
                    </form>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">コメント一覧 (最新50件)</div>
                <div class="card-body">
                    @if ($comments->isEmpty())
                        <p>まだコメントはありません。</p>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach ($comments as $comment)
                                <li class="list-group-item">
                                    <p class="mb-1">{{ $comment->content }}</p>
                                    <small class="text-muted">投稿日時: {{ $comment->created_at->format('Y/m/d H:i') }}</small>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const commentForm = document.getElementById('comment-form');
    commentForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const fighterId = {{ $fighter->id }};
        const content = document.getElementById('content').value;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch('/api/comments', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                fighter_id: fighterId,
                content: content
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                // コメント投稿後、ページをリロードして最新のコメントを表示
                window.location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('コメント投稿中にエラーが発生しました。');
        });
    });
});
</script>
@endsection