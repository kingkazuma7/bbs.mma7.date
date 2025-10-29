@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ __('messages.events_list') }}</h1>
    <a href="{{ route('events.create') }}" class="btn btn-primary mb-3">{{ __('messages.create_new_event') }}</a>
    <ul class="list-group">
        @foreach($events as $event)
            <li class="list-group-item">
                <a href="{{ route('events.show', $event) }}">{{ $event->name }}</a>
            </li>
        @endforeach
    </ul>
</div>
@endsection
