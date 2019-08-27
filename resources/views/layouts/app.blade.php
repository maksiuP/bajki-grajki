<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Bajki Grajki') }}</title>

    <script src="{{ mix('js/app.js') }}" defer></script>
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-900 text-gray-200">
    <div id="app" class="container my-4 flex flex-col sm:flex-row">

        <div class="flex-none h-12 w-full mb-4 sm:h-full sm:w-12 sm:mb-0 sm:mr-4">
            <a href="{{ route('tales.index') }}" alt="Bajki Grajki">
                <img src="https://img.icons8.com/windows/64/edf2f7/music-record.png" class="w-12 h-12 inline">
            </a>
            <a href="{{ route('artists.index') }}" alt="Artyści">
                <img src="https://img.icons8.com/windows/64/edf2f7/person-male.png" class="w-12 h-12 inline">
            </a>
            @guest
                <a href="{{ route('login') }}" alt="Login">
                    <img src="https://img.icons8.com/windows/64/edf2f7/login-rounded-right.png" class="w-12 h-12 inline">
                </a>
            @else
                <a href="{{ route('tales.create') }}" alt="Nowa bajka">
                    <img src="https://img.icons8.com/windows/64/edf2f7/add.png" class="w-12 h-12 inline">
                </a>
                <a href="{{ route('logout') }}"
                    onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();"
                    alt="{{ Auth::user()->name }}">
                    <img src="https://img.icons8.com/windows/64/edf2f7/logout-rounded-left.png" class="w-12 h-12 inline">
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            @endguest
        </div>

        <main class="flex-1">
            @yield('content')
        </main>
    </div>
</body>
</html>
