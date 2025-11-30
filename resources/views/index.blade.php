@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column">
        {{-- Navbar --}}
        <header class="bg-brand-purple">
            <nav class="navbar navbar-expand-md shadow">
                <div class="container">
                    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav">
                        <i class="navbar-toggler-icon"></i>
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

                        <div
                            class="ms-auto d-flex gap-3 justify-content-start justify-content-md-center align-items-center">
                            @guest
                                <a href="{{ route('login') }}" class="btn btn-primary">Prihlásiť sa</a>
                                <a href="{{ route('register') }}" class="btn btn-warning">Registrovať sa</a>
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

        {{-- Hero --}}
        <section class="p-5 d-flex flex-column flex-xl-row gap-5 justify-content-xl-between">
            {{-- Hero content --}}
            <div class="animate__animated animate__bounceInLeft d-flex flex-column gap-3" style="max-width: 670px;">
                <img src="/img/logo-text.png" alt="" class="mb-3"
                    style="max-width: 320px; width: 100%; height: auto;">

                <div class="p-4 shadow-sm bg-light d-flex flex-column gap-3">
                    <div class="d-flex">
                        <i class="bx bxs-code-alt fs-3 text-primary me-3"></i>
                        <p class="mb-0 align-self-center fw-bold">Nauč sa Python, krok za krokom.</p>
                    </div>

                    <div class="d-flex">
                        <i class="bx bxs-layers fs-3 text-primary me-3"></i>
                        <p class="mb-0 align-self-center">Interaktívna platforma, ktorá spája lekcie, príklady, spúšťanie
                            kódu a cvičenia do
                            jedného miesta.</p>
                    </div>

                    <div class="d-flex">
                        <i class="bx bxs-cloud fs-3 text-primary me-3"></i>
                        <p class="mb-0 align-self-center">Nepotrebuješ inštalovať Python, všetko beží v prehliadači.</p>
                    </div>

                    <div class="d-flex">
                        <i class="bx bxs-check-circle fs-3 text-primary me-3"></i>
                        <p class="mb-0 align-self-center fw-bold">Prístup ku všetkým lekciam úplne zadarmo.</p>
                    </div>
                </div>
            </div>

            {{-- Hero carousel --}}
            <div id="hero-carousel"
                class="animate__animated animate__zoomIn carousel slide align-self-center align-self-xl-auto">
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#hero-carousel" data-bs-slide-to="0" class="active"
                        aria-current="true" aria-label="Slide 1"></button>
                    <button type="button" data-bs-target="#hero-carousel" data-bs-slide-to="1"
                        aria-label="Slide 2"></button>
                    <button type="button" data-bs-target="#hero-carousel" data-bs-slide-to="2"
                        aria-label="Slide 3"></button>
                </div>
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="https://placehold.co/600x400/png" class="d-block w-100" alt="...">
                        <div class="carousel-caption d-none d-md-block">
                            <h5>First slide label</h5>
                            <p>Some representative placeholder content for the first slide.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="https://placehold.co/600x400/png" class="d-block w-100" alt="...">
                        <div class="carousel-caption d-none d-md-block">
                            <h5>Second slide label</h5>
                            <p>Some representative placeholder content for the second slide.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="https://placehold.co/600x400/png" class="d-block w-100" alt="...">
                        <div class="carousel-caption d-none d-md-block">
                            <h5>Third slide label</h5>
                            <p>Some representative placeholder content for the third slide.</p>
                        </div>
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#hero-carousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#hero-carousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </section>

        <div class="hero-divider"></div>

        {{-- Features --}}
        <section class="index-section bg-brand-blue py-5">
            <div class="container">
                <h2 class="text-center mb-5 fw-bold">Uč sa Python kedy chceš, kde chceš</h2>

                <div class="row g-4">
                    <div class="col-md-6 col-lg-3">
                        <div class="p-4 bg-light shadow-sm rounded h-100 text-center">
                            <i class="bx bx-book-open fs-1 text-primary mb-3"></i>
                            <h5 class="fw-bold mb-2">Lekcie</h5>
                            <p class="text-muted mb-0">
                                Stručné, jasné vysvetlenia s praktickými ukážkami kódu v každej téme.
                            </p>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <div class="p-4 bg-light shadow-sm rounded h-100 text-center">
                            <i class="bx bx-terminal fs-1 text-primary mb-3"></i>
                            <h5 class="fw-bold mb-2">Integrovaný editor</h5>
                            <p class="text-muted mb-0">
                                Spúšťaj Python priamo v prehliadači bez inštalácie a bez konfigurácie.
                            </p>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <div class="p-4 bg-light shadow-sm rounded h-100 text-center">
                            <i class="bx bx-edit fs-1 text-primary mb-3"></i>
                            <h5 class="fw-bold mb-2">Cvičenia</h5>
                            <p class="text-muted mb-0">
                                Písanie vlastného kódu s okamžitou spätnou väzbou a kontrolou riešení.
                            </p>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <div class="p-4 bg-light shadow-sm rounded h-100 text-center">
                            <i class="bx bx-check fs-1 text-primary mb-3"></i>
                            <h5 class="fw-bold mb-2">Kvízy</h5>
                            <p class="text-muted mb-0">
                                Krátke testy na overenie pochopenia tém a rýchle opakovanie.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mt-4">
                    <div class="col-md-6 col-lg-4">
                        <div class="p-4 bg-light shadow-sm rounded h-100 text-center">
                            <i class="bx bx-bar-chart fs-1 text-primary mb-3"></i>
                            <h5 class="fw-bold mb-2">XP a levely</h5>
                            <p class="text-muted mb-0">
                                Získavaj XP za dokončené úlohy a sleduj svoj rast v reálnom čase.
                            </p>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <div class="p-4 bg-light shadow-sm rounded h-100 text-center">
                            <i class="bx bx-user fs-1 text-primary mb-3"></i>
                            <h5 class="fw-bold mb-2">Profil & Pokrok</h5>
                            <p class="text-muted mb-0">
                                Sleduj, čo si už zvládol, a pokračuj tam, kde si skončil.
                            </p>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <div class="p-4 bg-light shadow-sm rounded h-100 text-center">
                            <i class="bx bx-git-branch fs-1 text-primary mb-3"></i>
                            <h5 class="fw-bold mb-2">Postupné budovanie zručností</h5>
                            <p class="text-muted mb-0">
                                Každá ďalšia lekcia nadväzuje na predchádzajúcu, aby si nepremárnil čas.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="features-divider"></div>

        {{-- Latest lectures --}}
        <section class="py-5 bg-light">
            <div class="container">
                <h2 class="fw-bold text-center mb-5">Najnovšie lekcie</h2>

                <div class="row g-4">
                    @foreach ($latestLectures as $lecture)
                        <div class="col-md-4">
                            <a href="{{ route('lectures.show', $lecture->id) }}" class="text-decoration-none text-dark">
                                <div class="p-4 bg-white shadow-sm rounded h-100">
                                    <h5 class="fw-bold">{{ $lecture->title }}</h5>
                                    <p class="text-muted">{{ Str::limit($lecture->description, 120) }}</p>
                                    <span class="text-primary fw-semibold">Čítať viac →</span>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Footer --}}
        <footer class="py-5 bg-dark text-white">
            <div class="container d-flex flex-column flex-md-row justify-content-between">

                <div>
                    <h5 class="fw-bold">Pythonškola</h5>
                    <p class="text-white-50">Interaktívna platforma na učenie Pythonu.</p>
                </div>

                <div>
                    <h6 class="fw-bold">Obsah</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('lectures.index') }}" class="text-white-50">Lekcie</a></li>
                        <li><a href="{{ route('exercises.index') }}" class="text-white-50">Cvičenia</a></li>
                        <li><a href="{{ route('quizzes.index') }}" class="text-white-50">Kvízy</a></li>
                    </ul>
                </div>

                <div>
                    <h6 class="fw-bold">Účet</h6>
                    <ul class="list-unstyled">
                        @guest
                            <li><a href="{{ route('login') }}" class="text-white-50">Prihlásenie</a></li>
                            <li><a href="{{ route('register') }}" class="text-white-50">Registrácia</a></li>
                        @endguest
                        @auth
                            <li><a href="{{ auth()->user()->profile_url }}" class="text-white-50">Profil</a></li>
                        @endauth
                    </ul>
                </div>

            </div>

            <div class="text-center text-white-50 mt-4">
                © {{ date('Y') }} Pythonškola. Všetky práva vyhradené.
            </div>
        </footer>
    </div>
@endsection
