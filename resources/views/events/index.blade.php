@extends('layouts.app')

@section('content')
<div class="container">
    <h1>大会一覧</h1>
    <a href="{{ route('events.create') }}" class="btn btn-primary mb-3">新規大会作成</a>
    <ul class="list-group">
        @foreach($events as $event)
            <li class="list-group-item">
                <a href="{{ route('events.show', $event) }}">{{ $event->name }}</a>
            </li>
        @endforeach
    </ul>
</div>
@endsection
