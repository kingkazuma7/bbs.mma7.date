<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="google-site-verification" content="6SNLD4Z1k89AAcyohUG5cpxsqiCxqkX12VetSTQVdIo" />
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

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

    <meta name="twitter:card" content="summary_large_image">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="ja_JP">
    <meta property="og:site_name" content="ガチ格｜格闘家「強い・弱い」みんなのホンネが集まる掲示板">

    <meta name="robots" content="index, follow">
    <meta name="format-detection" content="telephone=no">
    <meta name="theme-color" content="#ffffff">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/scss/app.scss'])
    <script src="{{ asset('js/main.js') }}?v={{ time() }}" defer></script>
</head>
<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand text-wrap me-0" href="{{ url('/') }}">
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
                    <div class="position-relative">
                        <form class="d-flex" action="{{ url('/search') }}" method="GET">
                            <input class="form-control me-2" type="text" name="q" id="search_form" value="{{ request('q') }}" placeholder="人物名・グループ名" autocomplete="off">
                            <button class="btn btn-success text-nowrap" type="submit" id="search_btn">
                                <i class="fa fa-search"></i> 検索
                            </button>
                        </form>
                        <!-- 検索おすすめ（レコメンド）ポップアップ -->
                        <div id="search-recommendation" class="dropdown-menu w-100 shadow-sm mt-1" style="display: none; position: absolute; z-index: 1050; border-radius: 8px;">
                            <div class="px-3 py-2 text-muted fw-bold border-bottom" style="font-size: 0.85rem; background-color: #f8f9fa; border-top-left-radius: 8px; border-top-right-radius: 8px;">
                                <i class="fas fa-fire text-danger me-1"></i> トレンドキーワード
                            </div>
                            <!-- TODO: データ集まり次第、Controller等から動的に人気キーワードを渡す仕様に変更する -->
                            <ul class="list-unstyled mb-0 pb-1" style="padding-left: 0; margin: 0;">
                                <li style="list-style: none;"><a href="{{ url('/search?q=朝倉未来') }}" class="dropdown-item py-2"><i class="fas fa-search text-muted me-2" style="font-size: 0.8rem;"></i>朝倉未来</a></li>
                                <li style="list-style: none;"><a href="{{ url('/search?q=井上尚弥') }}" class="dropdown-item py-2"><i class="fas fa-search text-muted me-2" style="font-size: 0.8rem;"></i>井上尚弥</a></li>
                                <li style="list-style: none;"><a href="{{ url('/search?q=平本蓮') }}" class="dropdown-item py-2"><i class="fas fa-search text-muted me-2" style="font-size: 0.8rem;"></i>平本蓮</a></li>
                                <li style="list-style: none;"><a href="{{ url('/search?q=堀口恭司') }}" class="dropdown-item py-2"><i class="fas fa-search text-muted me-2" style="font-size: 0.8rem;"></i>堀口恭司</a></li>
                                <li style="list-style: none;"><a href="{{ url('/search?q=朝倉海') }}" class="dropdown-item py-2"><i class="fas fa-search text-muted me-2" style="font-size: 0.8rem;"></i>朝倉海</a></li>
                            </ul>
                        </div>
                    </div>
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
