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
            <div class="" style="max-width: 670px;">
                <img src="/img/logo-text.png" alt="" class="mb-3"
                    style="max-width: 320px; width: 100%; height: auto;">
                <p>
                    Nauč sa Python, krok za krokom.
                </p>
                <p>
                    Interaktívna platforma, ktorá spája lekcie, príklady, spúšťanie kódu a cvičenia do jedného miesta.
                </p>
                <p>
                    Nepotrebuješ inštalovať Python, všetko beží v prehliadači.
                </p>
            </div>

            <div id="hero-carousel" class="carousel slide align-self-center align-self-xl-auto">
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

        <section class="index-section bg-brand-blue" style="">

        </section>
    </div>
@endsection
