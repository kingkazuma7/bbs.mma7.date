@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ __('messages.fighter_details') }}: {{ $fighter->name }}</h1>
    <p>{{ __('messages.fighter_id') }}: {{ $fighter->id }}</p>
    <p>{{ __('messages.created_at') }}: {{ $fighter->created_at }}</p>
    <p>{{ __('messages.updated_at') }}: {{ $fighter->updated_at }}</p>
    <a href="{{ route('fighters.index') }}" class="btn btn-primary">{{ __('messages.back_to_fighters') }}</a>
    <a href="{{ route('fighters.threads', $fighter) }}" class="btn btn-info">{{ __('messages.view_threads') }}</a>
</div>
@endsection
