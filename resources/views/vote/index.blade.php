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
    voteButtons.forEach(button => {
        button.addEventListener('click', function () {
            const fighterId = this.dataset.fighterId;
            const voteType = this.dataset.voteType;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

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
    }
});
</script>
@endsection