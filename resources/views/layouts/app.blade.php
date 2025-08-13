<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>PyTutor - nauč sa Python dnes!</title>

    <link href='https://cdn.boxicons.com/fonts/basic/boxicons.min.css' rel='stylesheet'>
    @vite(['resources/js/app.js'])
</head>

<body class="d-flex flex-column min-vh-100">
    <div id="app" class="flex-grow-1 d-flex flex-column">
        {{-- Alerts --}}
        <x-alerts />

        <header class="sticky-top bg-light">
            <nav class="navbar navbar-expand-md border-bottom">
                <div class="container">
                    <a href="{{ route('index') }}" class="navbar-brand">PyTutor</a>
                    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav">
                        <i class="navbar-toggler-icon"></i>
                    </button>
                    <div id="nav" class="collapse navbar-collapse justify-content-center">
                        <ul class="navbar-nav">
                            <li class="nav-item"><a href="#" class="nav-link">Lekcie</a></li>
                            <li class="nav-item"><a href="#" class="nav-link">Cvičenia</a></li>
                            <li class="nav-item"><a href="#" class="nav-link">Kvízy</a></li>
                        </ul>

                        <div
                            class="ms-auto d-flex gap-3 justify-content-start justify-content-md-center align-items-center">
                            @guest
                                <a href="{{ route('login') }}" class="btn btn-outline-primary">Prihlásiť sa</a>

                                <a href="{{ route('register') }}" class="btn btn-primary">
                                    Registrovať sa
                                </a>
                            @endguest
                            @auth
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    @method('POST')

                                    <button type="submit" class="nav-link link-primary">Odhlásiť sa</button>
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

        <main class="flex-grow-1 row g-0">
            <aside class="d-none d-lg-block col-md-2 border-end pe-0">
                <div class="list-group list-group-flush list-group-numbered">
                    <a href="#" class="list-group-item border-bottom active px-2 py-1">
                        Čo je to Python?
                    </a>
                    <a href="#" class="list-group-item border-bottom px-2 py-1">
                        Základy
                    </a>
                    <a href="#" class="list-group-item border-bottom px-2 py-1">
                        Premenné a dátové typy
                    </a>
                </div>
            </aside>

            <div class="col-12 col-lg-10 container py-3">
                @yield('content')
            </div>
        </main>
    </div>


    {{-- Completed quizzes --}}
    @if ($completedQuizzes)
        <script>
            window.completedQuizzes = @json($completedQuizzes);
        </script>
    @endif
</body>

</html>
