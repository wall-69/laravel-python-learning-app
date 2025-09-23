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
            <nav class="navbar navbar-expand-md border-bottom">
                <div class="container">
                    {{-- Category lectures offcanvas menu toggler (only visible < md) --}}
                    @if (empty($hideSidebar) && isset($categoryLectures))
                        <button class="d-flex justify-content-center align-items-center d-md-none navbar-toggler"
                            type="button" data-bs-toggle="offcanvas" data-bs-target="#categoryLecturesOffcanvas"
                            aria-controls="categoryLecturesOffcanvas">
                            <i class="bx bx-dock-right-arrow bx-md"></i>
                        </button>
                    @endif

                    <a href="{{ route('index') }}" class="navbar-brand me-0 me-md-3">
                        <x-logo />
                    </a>
                    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav">
                        <i class="navbar-toggler-icon"></i>
                    </button>
                    <div id="nav" class="collapse navbar-collapse justify-content-center">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a href="{{ route('lectures.index') }}" class="nav-link @active('lectures.index')">Lekcie</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('exercises.index') }}" class="nav-link @active('exercises.index')">Cvičenia</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('quizzes.index') }}" class="nav-link @active('quizzes.index')">Kvízy</a>
                            </li>
                        </ul>

                        <div
                            class="ms-auto d-flex gap-3 justify-content-start justify-content-md-center align-items-center">
                            @guest
                                <a href="{{ route('login') }}" class="btn btn-outline-primary">Prihlásiť sa</a>
                                <a href="{{ route('register') }}" class="btn btn-primary">Registrovať sa</a>
                            @endguest
                            @auth
                                <a href="{{ auth()->user()->profile_url }}" class="text-decoration-none">Môj profil</a>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
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
            @if (empty($hideSidebar) && isset($categoryLectures))
                {{-- Normal sidebar (md+) --}}
                <aside class="d-none d-md-block col-md-2 border pe-0">
                    <div class="list-group list-group-flush list-group-numbered">
                        @foreach ($categoryLectures as $categoryLecture)
                            <a href="{{ route('lectures.show', [$categoryLecture, $categoryLecture->slug]) }}"
                                class="list-group-item border-bottom px-2 py-1 @if ($lecture->id == $categoryLecture->id) active @endif">
                                {{ $categoryLecture->title }}
                            </a>
                        @endforeach
                    </div>
                </aside>

                {{-- Offcanvas sidebar (< md) --}}
                <div class="offcanvas offcanvas-start d-md-none" tabindex="-1" id="categoryLecturesOffcanvas"
                    aria-labelledby="categoryLecturesOffcanvasLabel">
                    <div class="offcanvas-header">
                        <h5 id="categoryLecturesOffcanvasLabel">Lekcie</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body p-0">
                        <div class="list-group list-group-flush list-group-numbered">
                            @foreach ($categoryLectures as $categoryLecture)
                                <a href="{{ route('lectures.show', [$categoryLecture, $categoryLecture->slug]) }}"
                                    class="list-group-item border-bottom px-2 py-1 @if ($lecture->id == $categoryLecture->id) active @endif">
                                    {{ $categoryLecture->title }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <div class="col-12 col-md-10 container py-3">
                @yield('content')
            </div>
        </main>
    </div>

    {{-- Completed quizzes & exercises --}}
    <script>
        window.completedQuizzes = @json($completedQuizzes ?? []);
        window.completedExercises = @json($completedExercises ?? []);
    </script>
</body>

</html>
