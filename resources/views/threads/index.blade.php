@extends('layouts.app')

@section('content')
<div class="container">
    <h1>スレッド一覧</h1>
    <a href="{{ route('threads.create') }}" class="btn btn-primary mb-3">新規スレッド作成</a>
    <ul class="list-group">
        @foreach($threads as $thread)
            <li class="list-group-item">
                <a href="{{ route('threads.show', $thread) }}">{{ $thread->title }}</a>
            </li>
        @endforeach
    </ul>
</div>
@endsection
