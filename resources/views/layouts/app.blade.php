<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="google-site-verification" content="6SNLD4Z1k89AAcyohUG5cpxsqiCxqkX12VetSTQVdIo" />

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-5VMVNQVCFB"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-5VMVNQVCFB');
    </script>

    @if(isset($seoModel))
        {!! seo()->for($seoModel) !!}
    @elseif(isset($seoData))
        {!! seo()->for($seoData) !!}
    @else
        {!! seo() !!}
    @endif

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
                    @auth
                        @if(auth()->user()->is_admin)
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('/admin/fighters') }}">選手管理</a>
                            </li>
                        @endif
                    @endauth
                </ul>
                <div class="d-flex ms-auto align-items-center gap-2">
                    @auth
                        <span class="navbar-text me-3">{{ auth()->user()->name }} さん</span>
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm">ログアウト</button>
                        </form>
                    @else
                        <a href="{{ url('/login') }}" class="btn btn-primary btn-sm">ログイン</a>
                    @endauth
                    <form class="d-flex" action="{{ url('/search') }}" method="GET">
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
