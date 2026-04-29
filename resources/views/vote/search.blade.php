@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h1 class="h2 mb-4">検索結果</h1>

            @if (empty($query))
                <div class="alert alert-info" role="alert">
                    検索キーワードを入力してください。
                </div>
            @elseif ($results->isEmpty())
                <div class="alert alert-warning" role="alert">
                    「{{ $query }}」に該当する格闘家は見つかりません。
                </div>
            @else
                <p class="mb-4">「<strong>{{ $query }}</strong>」の検索結果: <strong>{{ $results->count() }}</strong> 件</p>

                <div class="row g-2 g-md-3">
                    @foreach ($results as $fighter)
                        <div class="col-4 col-sm-4 col-md-3 col-lg-2">
                            <a href="{{ url('/') }}?id={{ $fighter->id }}" class="text-decoration-none text-dark">
                                <div class="card h-100 hover-shadow" style="transition: box-shadow 0.3s;">
                                    @if ($fighter->image_path)
                                        <img src="{{ $fighter->image_path }}" class="card-img-top" alt="{{ $fighter->name }}" style="height: 100px; object-fit: contain; object-position: top;">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 100px; border-bottom: 1px solid #dee2e6;">
                                            <small class="text-muted">画像なし</small>
                                        </div>
                                    @endif
                                    <div class="card-body p-2 text-center">
                                        <p class="card-text fw-bold small mb-0 text-truncate">{{ $fighter->name }}</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="mt-4">
                <a href="{{ url('/') }}" class="btn btn-secondary">投票ページに戻る</a>
            </div>
        </div>
    </div>
</div>
@endsection
