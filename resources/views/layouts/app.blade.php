<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>ガチ格 ─格闘家の「強い・弱い」本音掲示板─</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @if(App::environment() !== 'production')
        @vite('resources/scss/app.scss')
    @else
        <link href="{{ asset('build/assets/app-23NSH9ET.css') }}" rel="stylesheet">
    @endif
    <script src="{{ asset('js/main.js') }}" defer></script>
</head>
<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                ガチ格｜格闘家「強い・弱い」
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/') }}">投票ページ</a>
                    </li>
                </ul>
                <form class="d-flex ms-auto" action="{{ url('/search') }}" method="GET">
                    <input class="form-control me-2" type="text" name="q" id="search_form" value="{{ request('q') }}" placeholder="人物名・グループ名" autocomplete="off">
                    <button class="btn btn-success" type="submit" id="search_btn" style="white-space: nowrap;">
                        <i class="fa fa-search"></i> 検索
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <main class="py-4 flex-shrink-0">
        @yield('content')
    </main>

    @include('layouts.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
