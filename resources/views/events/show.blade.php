@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ __('messages.event_details') }}: {{ $event->name }}</h1>
    <p>{{ __('messages.event_id') }}: {{ $event->id }}</p>
    <p>{{ __('messages.created_at') }}: {{ $event->created_at }}</p>
    <p>{{ __('messages.updated_at') }}: {{ $event->updated_at }}</p>
    <a href="{{ route('events.index') }}" class="btn btn-primary">{{ __('messages.back_to_events') }}</a>
</div>
@endsection
