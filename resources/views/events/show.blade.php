@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Event Details: {{ $event->name }}</h1>
    <p>Event ID: {{ $event->id }}</p>
    <p>Created At: {{ $event->created_at }}</p>
    <p>Updated At: {{ $event->updated_at }}</p>
    <a href="{{ route('events.index') }}" class="btn btn-primary">Back to Events</a>
</div>
@endsection
