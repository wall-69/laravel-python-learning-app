@props(['lecture', 'categoryLectures'])

<header class="sticky-top bg-brand-purple">
    <nav class="navbar navbar-expand-md shadow">
        <div class="container">
            @if (isset($categoryLectures))
                <button
                    class="d-flex d-md-none justify-content-center fw-bold gap-1 align-items-center bg-transparent text-white border-0"
                    data-bs-toggle="offcanvas" data-bs-target="#categoryLecturesOffcanvas">
                    <i class="bx bxs-bookmark bx-md"></i> {{ $lecture->category->title }}
                </button>
            @endif

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

                <div class="ms-auto d-flex gap-3 justify-content-start justify-content-md-center align-items-center">
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
