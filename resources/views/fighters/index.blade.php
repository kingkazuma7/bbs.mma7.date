@extends('layouts.app')

@section('content')
<div class="container">
    <h1>選手一覧</h1>
    <ul class="list-group">
        @foreach($fighters as $fighter)
            <li class="list-group-item">
                <a href="{{ route('fighters.show', $fighter) }}">{{ $fighter->name }}</a>
            </li>
        @endforeach
    </ul>
</div>
@endsection
