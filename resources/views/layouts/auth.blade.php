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

        <header class="bg-brand-purple sticky-top">
            <nav class="navbar navbar-expand-md shadow">
                <div class="container">
                    <button class="ms-auto bg-transparent text-white border-0 d-md-none align-items-center d-flex"
                        data-bs-toggle="collapse" data-bs-target="#nav">
                        <i class="bx bx-menu bx-lg"></i>
                    </button>
                    <div id="nav" class="collapse navbar-collapse justify-content-start">
                        <ul class="navbar-nav">
                            <li>

                            </li>
                            <li class="nav-item">
                                <a href="{{ route('lectures.index') }}"
                                    class="nav-link text-white text-decoration-underline">Lekcie</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('exercises.index') }}"
                                    class="nav-link text-white text-decoration-underline">Cvičenia</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('quizzes.index') }}"
                                    class="nav-link text-white text-decoration-underline">Kvízy</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>

        <main class="flex-grow-1 row g-0">
            @yield('form')
        </main>

        <x-footer />
    </div>
</body>

</html>
