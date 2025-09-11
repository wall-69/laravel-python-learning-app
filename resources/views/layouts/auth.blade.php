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
                    <a href="{{ route('index') }}" class="navbar-brand">{{ env('APP_NAME') }}</a>
                </div>
            </nav>
        </header>

        <main class="flex-grow-1 row g-0">
            @yield('form')
        </main>
    </div>
</body>

</html>
