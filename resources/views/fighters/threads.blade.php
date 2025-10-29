@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ __('messages.threads_for_fighter', ['name' => $fighter->name]) }}</h1>
    <p>{{ __('messages.fighter_id') }}: {{ $fighter->id }}</p>

    <h2 class="mt-4">{{ __('messages.related_threads') }}</h2>
    @if($fighter->threads->isEmpty())
        <p>{{ __('messages.no_threads_found') }}</p>
    @else
        <ul class="list-group">
            @foreach($fighter->threads as $thread)
                <li class="list-group-item">
                    <a href="{{ route('threads.show', $thread) }}">{{ $thread->title }}</a>
                </li>
            @endforeach
        </ul>
    @endif
    <a href="{{ route('fighters.show', $fighter) }}" class="btn btn-primary">{{ __('messages.back_to_fighter_details') }}</a>
</div>
@endsection
