<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/x-icon" href="/img/favicon2.ico" />

    <title>{{ env('APP_NAME') }}</title>

    <link href='https://cdn.boxicons.com/fonts/basic/boxicons.min.css' rel='stylesheet'>
    @vite(['resources/js/app.js'])
</head>

<body class="d-flex flex-column min-vh-100">
    <div id="app" class="flex-grow-1 d-flex flex-column">
        {{-- Alerts --}}
        <x-alerts />

        <x-header :lecture="null" :category-lectures="null" />

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
