@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h1 class="h2 mb-4 text-center">{{ $fighter->name }} の詳細</h1>

            @if ($fighter->image_path)
                <div class="d-flex justify-content-center mb-4">
                    <img src="{{ $fighter->image_path }}" class="img-fluid rounded" alt="{{ $fighter->name }}" style="max-height: 400px; object-fit: contain; object-position: center;">
                </div>
            @else
                <div class="bg-light d-flex align-items-center justify-content-center mb-4" style="width: 100%; height: 400px; border-radius: .25rem;">
                    <span class="text-muted">画像なし</span>
                </div>
            @endif

            <div class="card mb-4">
                <div class="card-header">投票統計</div>
                <div class="card-body">
                    <p><strong>強い:</strong> {{ $stats['strong_count'] }} 票 ({{ $stats['strong_percentage'] }}%)</p>
                    <div class="progress mb-2">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $stats['strong_percentage'] }}%;" aria-valuenow="{{ $stats['strong_percentage'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p><strong>弱い:</strong> {{ $stats['weak_count'] }} 票 ({{ $stats['weak_percentage'] }}%)</p>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $stats['weak_percentage'] }}%;" aria-valuenow="{{ $stats['weak_percentage'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p><strong>合計投票数:</strong> {{ $stats['total_count'] }} 票</p>
                </div>
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

<div class="container">
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

<div class="container mb-5">
    <div class="mt-5 pt-4 border-top">
        <h3 class="h5 mb-4 fw-bold text-secondary text-center text-md-start"><i class="fas fa-fire text-danger me-2"></i>話題の格闘家をチェック</h3>
        <div class="d-flex flex-wrap gap-2 justify-content-center justify-content-md-start">
            @foreach ($topFighters as $topFighter)
                <a href="{{ route('people.vote', ['fighterName' => $topFighter->name]) }}" class="btn btn-light border rounded-pill px-3 px-md-4 py-2 hover-shadow transition-all" style="font-weight: 600; font-size: 0.9rem;">
                    <span class="text-primary me-1">#</span>{{ $topFighter->name }}
                </a>
            @endforeach
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