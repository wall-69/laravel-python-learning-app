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
        <div class="position-absolute z-3" style="top: 80px; left: 50%; transform: translateX(-50%)">
            @session('success')
                <x-alert type="success" :message="session('success')"></x-alert>
            @endsession
            @session('warning')
                <x-alert type="warning" :message="session('warning')"></x-alert>
            @endsession
            @session('danger')
                <x-alert type="danger" :message="session('danger')"></x-alert>
            @endsession
        </div>

        <header class="sticky-top bg-light">
            <nav class="navbar navbar-expand-md border-bottom">
                <div class="container">
                    <a href="{{ route('index') }}" class="navbar-brand">PyTutor</a>
                </div>
            </nav>
        </header>

        <main class="flex-grow-1 row g-0">
            @yield('form')
        </main>
    </div>
</body>

</html>
