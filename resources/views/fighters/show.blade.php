@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ $fighter->name }} のスレッド</h1>
    <p>ファイターID: {{ $fighter->id }}</p>

    <h2 class="mt-4">関連スレッド一覧</h2>
    @if($fighter->threads->isEmpty())
        <p>このファイターに関連するスレッドはありません。</p>
    @else
        <ul class="list-group">
            @foreach($fighter->threads as $thread)
                <li class="list-group-item">
                    <a href="{{ route('threads.show', $thread) }}">{{ $thread->title }}</a>
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection
