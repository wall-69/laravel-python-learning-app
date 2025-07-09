<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>PyTutor - nauč sa Python dnes!</title>

    @vite(['resources/js/app.js'])
</head>

<body class="d-flex flex-column" style="min-height: 100svh;">
    <div id="app" class="flex-grow-1 d-flex flex-column">
        <header class="sticky-top">
            <nav
                class="container-fluid d-flex flex-wrap align-items-center justify-content-center justify-content-md-evenly py-3">
                <a href="#">PyTutor</a>
                <ul class="nav col-12 col-md-auto mb-2 justify-content-center mb-md-0">
                    <li><a href="#" class="nav-link px-2">Home</a></li>
                    <li><a href="#" class="nav-link px-2">Features</a></li>
                    <li><a href="#" class="nav-link px-2">Pricing</a></li>
                    <li><a href="#" class="nav-link px-2">FAQs</a></li>
                    <li><a href="#" class="nav-link px-2">About</a></li>
                </ul>
                <div class="text-end">
                    <button type="button" class="btn btn-outline-primary me-2">Prihlásiť sa</button>

                    <button type="button" class="btn btn-primary">
                        Registrovať sa
                    </button>
                </div>
            </nav>
        </header>

        <main class="flex-grow-1">
            <aside>

            </aside>

            <div>
                @yield('content')
            </div>
        </main>
    </div>
</body>

</html>
