<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="/img/favicon2.ico" />

    <title>{{ env('APP_NAME') }} - Admin</title>

    <link href='https://cdn.boxicons.com/fonts/basic/boxicons.min.css' rel='stylesheet'>
    @vite(['resources/js/admin.js', 'resources/js/app.js'])
</head>

<body class="d-flex flex-column min-vh-100">
    <div id="app" class="flex-grow-1 d-flex flex-column">
        {{-- Alerts --}}
        <x-alerts />

        <main class="flex-grow-1 d-flex g-0">
            <aside class="admin-sidebar p-0 p-md-3 border-end mh-100">
                <div class="d-flex flex-column">
                    <h3 class="d-none d-md-inline mb-0">
                        <a href="{{ route('index') }}" class="text-decoration-none d-flex justify-content-center">
                            <x-logo />
                        </a>
                    </h3>

                    <hr class="d-none d-md-block">

                    <ul class="d-none d-md-flex nav nav-pills flex-column">
                        <li class="nav-item">
                            <a href="{{ route('admin.dashboard') }}"
                                class="@active('admin.dashboard') nav-link d-flex align-items-center gap-2">
                                <i class="bx bxs-tachometer bx-md"></i>
                                Panel
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.lectures') }}"
                                class="@active('admin.lectures') nav-link d-flex align-items-center gap-2">
                                <i class="bx bxs-article bx-md"></i>
                                Lekcie
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.categories') }}"
                                class="@active('admin.categories') nav-link d-flex align-items-center gap-2">
                                <i class="bx bxs-categories bx-md"></i>
                                Kategórie
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.users') }}"
                                class="@active('admin.users') nav-link d-flex align-items-center gap-2">
                                <i class="bx bxs-user bx-md"></i>
                                Používatelia
                            </a>
                        </li>
                    </ul>

                    <ul class="d-flex d-md-none nav nav-pills nav-fill flex-column" style="margin-top: 60px">
                        <li class="nav-item">
                            <a href="{{ route('admin.dashboard') }}"
                                class="@active('admin.dashboard') nav-link py-3 border-top border-bottom rounded-0 d-flex justify-content-center align-items-center gap-2">
                                <i class="bx bxs-tachometer bx-lg"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.lectures') }}"
                                class="@active('admin.lectures') nav-link py-3 border-bottom rounded-0 d-flex justify-content-center align-items-center gap-2">
                                <i class="bx bxs-article bx-lg"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.categories') }}"
                                class="@active('admin.categories') nav-link py-3 border-bottom rounded-0 d-flex justify-content-center align-items-center gap-2">
                                <i class="bx bxs-categories bx-lg"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.users') }}"
                                class="@active('admin.users') nav-link py-3 border-bottom rounded-0 d-flex justify-content-center align-items-center gap-2">
                                <i class="bx bxs-user bx-lg"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </aside>
            <div class="container-fluid py-3">
                @yield('content')
            </div>
        </main>
    </div>
</body>

</html>
