@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ __('messages.thread_details') }}: {{ $thread->title }}</h1>
    @if ($thread->event)
        <p>{{ __('messages.event_label') }}: {{ $thread->event->name }}</p>
    @endif
    @if ($thread->fighter)
        <p>{{ __('messages.fighter_label') }}: {{ $thread->fighter->name }}</p>
    @endif

    <h2 class="mt-4">{{ __('messages.posts_list') }}</h2>
    @if($thread->posts->isEmpty())
        <p>{{ __('messages.no_posts_found') }}</p>
    @else
        <ul class="list-group">
            @foreach($thread->posts as $post)
                <li class="list-group-item">
                    <strong>{{ $post->name ?? __('messages.anonymous') }}</strong>: {{ $post->message }}
                    <br><small>{{ $post->created_at->diffForHumans() }}</small>
                </li>
            @endforeach
        </ul>
    @endif

    <h2 class="mt-4">{{ __('messages.new_post') }}</h2>
    <form action="{{ route('threads.posts.store', $thread) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">{{ __('messages.name_label') }} ({{ __('messages.optional') }})</label>
            <input type="text" class="form-control" id="name" name="name">
        </div>
        <div class="mb-3">
            <label for="message" class="form-label">{{ __('messages.message_label') }}</label>
            <textarea class="form-control" id="message" name="message" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">{{ __('messages.post_button') }}</button>
    </form>
</div>
@endsection
