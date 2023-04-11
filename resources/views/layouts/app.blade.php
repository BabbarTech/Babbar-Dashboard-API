<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    {{ $head ?? null }}
</head>
<body>
    <div id="app">

        <x-flash-message />

        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                    <small class="text-muted">by</small>
                    <img src="/images/logo-babbar.png" alt="logo Babbar" class="me-2" />
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        @auth()
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('projects.index') }}">{{ __('resources.projects.index.title') }}</a>
                            </li>
                        @endauth
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    @isAdmin
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.api_tokens') }}">{{ __('Api Tokens') }}</a>
                                    </li>
                                    @endisAdmin
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                           onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                            {{ __('Logout') }}
                                        </a>
                                    </li>
                                </ul>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            {{ $slot ?? null }}
            @yield('content')
        </main>
    </div>
    {{ $footer ?? null }}

    <div class="container">
        <footer class="d-flex flex-wrap justify-content-between align-items-start py-3 my-4 border-top">
            <div class="col-md-4 d-flex align-items-start">
                <span class="mb-3 mb-md-0 text-muted">© {{ date('Y') }} {{ config('app.name', 'Laravel') }} <small class="text-muted">by</small> Babbar</span>
            </div>
            <div class="text-center">
                <a href="https://www.babbar.tech/" class="d-inline-block">
                    <img src="/images/logo-babbar.png" alt="logo Babbar" class="" height="45" />
                </a>
            </div>
            <div class="text-center">
                <a href="https://yourtext.guru/" class="d-inline-block ">
                    <img src="/images/logo-yourtextguru.png" alt="logo YourTextGuru" class="" height="45" />
                </a>
            </div>
        </footer>
    </div>
</body>
</html>
