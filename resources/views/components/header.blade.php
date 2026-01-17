@props(['lecture', 'categoryLectures'])

<header class="sticky-top bg-primary">
    <nav class="navbar navbar-expand-md shadow">
        <div class="container">
            @if (isset($categoryLectures))
                <button
                    class="d-flex d-md-none justify-content-center fw-bold gap-1 align-items-center bg-transparent text-white border-0"
                    data-bs-toggle="offcanvas" data-bs-target="#categoryLecturesOffcanvas">
                    <i class="bx bxs-bookmark bx-md"></i> {{ $lecture->category->title }}
                </button>
            @endif

            {{-- Icon --}}
            <a href="{{ route('index') }}" class="me-md-3">
                <x-icon />
            </a>
            <button class="ms-auto bg-transparent text-white border-0 d-md-none align-items-center d-flex"
                data-bs-toggle="collapse" data-bs-target="#nav">
                <i class="bx bx-menu bx-lg"></i>
            </button>
            <div id="nav" class="collapse navbar-collapse">
                <ul class="navbar-nav align-items-start align-items-md-center">
                    {{-- Links --}}
                    <li class="nav-item mt-md-1">
                        <a href="{{ route('lectures.index') }}"
                            class="nav-link text-white d-flex justify-content-center align-items-center gap-1">
                            <i class="bx bxs-article bx-md"></i>
                            Lekcie
                        </a>
                    </li>
                    <li class="nav-item mt-md-1">
                        <a href="{{ route('exercises.index') }}"
                            class="nav-link text-white d-flex justify-content-center align-items-center gap-1">
                            <i class="bx bxs-keyboard bx-md"></i>
                            Cvičenia
                        </a>
                    </li>
                    <li class="nav-item mt-md-1">
                        <a href="{{ route('quizzes.index') }}"
                            class="nav-link text-white d-flex justify-content-center align-items-center gap-1">
                            <i class="bx bxs-education bx-md"></i>
                            Kvízy
                        </a>
                    </li>
                </ul>

                <div class="ms-auto d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2">
                    @guest
                        <a href="{{ route('login') }}" class="btn btn-light my-2 my-md-0 mt-md-1">Prihlásiť sa</a>
                        <a href="{{ route('register') }}" class="btn btn-secondary my-2 my-md-0 mt-md-1">Registrovať sa</a>
                    @endguest
                    @auth
                        <a href="{{ auth()->user()->profile_url }}"
                            class="text-white text-decoration-none d-flex justify-content-center align-items-center gap-1 py-2 py-md-0 mt-md-1">
                            <i class="bx bxs-user bx-md"></i>
                            Môj profil
                        </a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="nav-link text-white d-flex justify-content-center align-items-center gap-1 py-2 py-md-0 mt-md-1">
                                <i class="bx bx-arrow-in-left-square-half bx-md"></i>
                                Odhlásiť sa
                            </button>
                        </form>
                        @if (auth()->user()->admin)
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary mt-md-1">Admin panel</a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </nav>
</header>
