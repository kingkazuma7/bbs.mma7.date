@extends('layouts.app')

@section('content')
<div class="card mb-3">
    <div class="card-header">使い方</div>
    <div class="card-body">
        <ul class="list-unstyled">
            <li>新しいスレッドを作成するには「新規スレッド作成」ボタンをクリックしてください。</li>
            <li>既存のスレッドを見るには、スレッドタイトルをクリックしてください。</li>
            <li>スレッド内で返信するには、スレッド詳細ページで投稿フォームを使用してください。</li>
            <li>完全匿名で投稿できます。</li>
        </ul>
    </div>
</div>
<div class="container">
    <h1>{{ __('messages.threads_list') }}</h1>
    <a href="{{ route('threads.create') }}" class="btn btn-primary mb-3">{{ __('messages.create_new_thread') }}</a>
    <ul class="list-group">
        @foreach($threads as $thread)
            <li class="list-group-item">
                <a href="{{ route('threads.show', $thread) }}">{{ $thread->title }}</a>
            </li>
        @endforeach
    </ul>
</div>
@endsection
