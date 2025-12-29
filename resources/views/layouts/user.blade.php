<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>{{ env('APP_NAME') }}</title>

    <link href='https://cdn.boxicons.com/fonts/basic/boxicons.min.css' rel='stylesheet'>
    @vite(['resources/js/app.js'])
</head>

<body class="d-flex flex-column min-vh-100">
    <div id="app" class="flex-grow-1 d-flex flex-column">
        {{-- Alerts --}}
        <x-alerts />

        <header class="sticky-top bg-light">
            <header class="bg-brand-purple sticky-top">
                <nav class="navbar navbar-expand-md shadow">
                    <div class="container">
                        <button class="ms-auto bg-transparent text-white border-0 d-md-none align-items-center d-flex"
                            data-bs-toggle="collapse" data-bs-target="#nav">
                            <i class="bx bx-menu bx-lg"></i>
                        </button>
                        <div id="nav" class="collapse navbar-collapse justify-content-center">
                            <ul class="navbar-nav">
                                <li class="nav-item">
                                    <a href="{{ route('lectures.index') }}"
                                        class="nav-link text-white text-decoration-underline @active('lectures.index')">Lekcie</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('exercises.index') }}"
                                        class="nav-link text-white text-decoration-underline @active('exercises.index')">Cvičenia</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('quizzes.index') }}"
                                        class="nav-link text-white text-decoration-underline @active('quizzes.index')">Kvízy</a>
                                </li>
                            </ul>

                            <div
                                class="ms-auto d-flex gap-3 justify-content-start justify-content-md-center align-items-center">
                                @guest
                                    <a href="{{ route('login') }}" class="btn btn-primary">Prihlásiť sa</a>
                                    <a href="{{ route('register') }}" class="btn btn-warning">Registrovať sa</a>
                                @endguest
                                @auth
                                    <a href="{{ auth()->user()->profile_url }}" class="text-decoration-none text-white">Môj
                                        profil</a>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="nav-link text-white">Odhlásiť sa</button>
                                    </form>
                                    @if (auth()->user()->admin)
                                        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">Admin panel</a>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </div>
                </nav>
            </header>
        </header>

        <main class="flex-grow-1 row g-0">
            <div class="col-12 col-lg-10 container py-3">
                @yield('content')
            </div>
        </main>

        <x-footer />
    </div>

    {{-- Completed quizzes & exercises --}}
    <script>
        window.completedQuizzes = @json($completedQuizzes ?? []);
        window.completedExercises = @json($completedExercises ?? []);
    </script>
</body>

</html>
