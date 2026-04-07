@extends('layouts.app')

@section('content')
<div class="container text-center">
    <h1 class="mb-4">どちらの格闘家がより強いですか？</h1>

    @if ($fighters->isEmpty())
        <div class="alert alert-warning" role="alert">
            現在、投票できる格闘家がいません。管理者が格闘家を追加するまでお待ちください。
        </div>
    @else
        <div class="row justify-content-center">
            <div class="col-md-5 d-flex flex-column align-items-center">
                @php $fighter1 = $fighters->first(); @endphp
                @if ($fighter1)
                    <h2 class="mb-3">{{ $fighter1->name }}</h2>
                    @if ($fighter1->image_url)
                        <img src="{{ asset('storage/' . $fighter1->image_url) }}" class="img-fluid rounded mb-3" alt="{{ $fighter1->name }}" style="max-height: 300px; object-fit: cover;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center" style="width: 100%; height: 300px; border-radius: .25rem;">
                            <span class="text-muted">画像なし</span>
                        </div>
                    @endif
                    <button class="btn btn-success btn-lg mt-3 vote-button" data-fighter-id="{{ $fighter1->id }}" data-vote-type="strong">強い！</button>
                    <a href="{{ url('/fighter/' . $fighter1->id) }}" class="btn btn-info btn-sm mt-2">統計を見る</a>
                @endif
            </div>

            <div class="col-md-2 d-flex align-items-center justify-content-center">
                <h2 class="text-danger">VS</h2>
            </div>

            <div class="col-md-5 d-flex flex-column align-items-center">
                @php $fighter2 = $fighters->last(); @endphp
                @if ($fighter2 && $fighter1 && $fighter1->id !== $fighter2->id) {{-- 同じ格闘家が選ばれないようにチェック --}}
                    <h2 class="mb-3">{{ $fighter2->name }}</h2>
                    @if ($fighter2->image_url)
                        <img src="{{ asset('storage/' . $fighter2->image_url) }}" class="img-fluid rounded mb-3" alt="{{ $fighter2->name }}" style="max-height: 300px; object-fit: cover;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center" style="width: 100%; height: 300px; border-radius: .25rem;">
                            <span class="text-muted">画像なし</span>
                        </div>
                    @endif
                    <button class="btn btn-danger btn-lg mt-3 vote-button" data-fighter-id="{{ $fighter2->id }}" data-vote-type="strong">強い！</button>
                    <a href="{{ url('/fighter/' . $fighter2->id) }}" class="btn btn-info btn-sm mt-2">統計を見る</a>
                @else
                    <div class="alert alert-info" role="alert">
                        もう一人の格闘家を読み込み中、または利用可能な格闘家が一人しかいません。
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-5">
            <button class="btn btn-primary btn-lg" onclick="window.location.reload();">次へ (新しい対戦)</button>
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
                    alert(data.message);
                    // 投票後、ページをリロードして新しい対戦を表示
                    window.location.reload();
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
});
</script>
@endsection