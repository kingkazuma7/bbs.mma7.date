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
            <label for="title" class="form-label">{{ __('messages.thread_title') }}</label>
            <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required>
        </div>
        <div class="mb-3">
            <label for="fighter_id" class="form-label">{{ __('messages.related_fighter') }} ({{ __('messages.optional') }})</label>
            <select class="form-select" id="fighter_id" name="fighter_id">
                <option value="">{{ __('messages.select_placeholder') }}</option>
                @foreach($fighters as $fighter)
                    <option value="{{ $fighter->id }}" {{ old('fighter_id') == $fighter->id ? 'selected' : '' }}>{{ $fighter->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">{{ __('messages.create') }}</button>
    </form>
</div>
@endsection
