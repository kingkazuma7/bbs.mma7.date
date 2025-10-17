@extends('layouts.app')

@section('content')
<div class="container">
    <h1>スレッド: {{ $thread->title }}</h1>
    @if ($thread->event)
        <p>大会: {{ $thread->event->name }}</p>
    @endif
    @if ($thread->fighter)
        <p>選手: {{ $thread->fighter->name }}</p>
    @endif

    <h2 class="mt-4">投稿一覧</h2>
    @if($thread->posts->isEmpty())
        <p>まだ投稿はありません。</p>
    @else
        <ul class="list-group">
            @foreach($thread->posts as $post)
                <li class="list-group-item">
                    <strong>{{ $post->name ?? '匿名' }}</strong>: {{ $post->message }}
                    <br><small>{{ $post->created_at->diffForHumans() }}</small>
                </li>
            @endforeach
        </ul>
    @endif

    <h2 class="mt-4">新規投稿</h2>
    <form action="{{ route('threads.posts.store', $thread) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">名前 (任意)</label>
            <input type="text" class="form-control" id="name" name="name">
        </div>
        <div class="mb-3">
            <label for="message" class="form-label">メッセージ</label>
            <textarea class="form-control" id="message" name="message" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">投稿</button>
    </form>
</div>
@endsection
