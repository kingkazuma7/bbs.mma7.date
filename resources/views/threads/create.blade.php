@extends('layouts.app')

@section('content')
<div class="container">
    <h1>新規スレッド作成</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('threads.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="title" class="form-label">スレッドタイトル</label>
            <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required>
        </div>
        <div class="mb-3">
            <label for="event_id" class="form-label">関連大会 (任意)</label>
            <select class="form-select" id="event_id" name="event_id">
                <option value="">選択してください</option>
                @foreach($events as $event)
                    <option value="{{ $event->id }}" {{ old('event_id') == $event->id ? 'selected' : '' }}>{{ $event->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="fighter_id" class="form-label">関連選手 (任意)</label>
            <select class="form-select" id="fighter_id" name="fighter_id">
                <option value="">選択してください</option>
                @foreach($fighters as $fighter)
                    <option value="{{ $fighter->id }}" {{ old('fighter_id') == $fighter->id ? 'selected' : '' }}>{{ $fighter->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">作成</button>
    </form>
</div>
@endsection
