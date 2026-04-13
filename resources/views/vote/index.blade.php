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
                    <button class="btn btn-primary btn-lg" onclick="window.location.reload();">次へ (新しい格闘家)</button>
                </div>
                <a href="{{ url('/fighter/' . $fighter->id) }}" class="btn btn-info btn-sm mt-3">統計を見る</a>
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

            fetch(`/fighters/${fighterId}/vote`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    fighter_id: fighterId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    alert(data.message);
                }
                if (data.votes_count !== undefined) {
                    document.getElementById(`votes-count-${fighterId}`).innerText = `現在の票数: ${data.votes_count}`;
                }
                // window.location.reload(); // 画面リロードは不要
            })
            .catch(error => {
                console.error('Error:', error);
                alert('投票処理中にエラーが発生しました。');
            });
        });
    });
});
</script>
@endsection