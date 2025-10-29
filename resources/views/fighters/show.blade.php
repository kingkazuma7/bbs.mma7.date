@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Fighter Details: {{ $fighter->name }}</h1>
    <p>Fighter ID: {{ $fighter->id }}</p>
    <p>Created At: {{ $fighter->created_at }}</p>
    <p>Updated At: {{ $fighter->updated_at }}</p>
    <a href="{{ route('fighters.index') }}" class="btn btn-primary">Back to Fighters</a>
    <a href="{{ route('fighters.threads', $fighter) }}" class="btn btn-info">View Threads</a>
</div>
@endsection
