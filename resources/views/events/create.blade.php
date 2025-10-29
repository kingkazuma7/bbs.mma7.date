@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ __('messages.create_new_event') }}</h1>
    <form action="{{ route('events.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">{{ __('messages.event_name') }}:</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <button type="submit" class="btn btn-primary">{{ __('messages.submit') }}</button>
    </form>
</div>
@endsection
