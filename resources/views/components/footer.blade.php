<footer class="py-5 bg-dark text-white">
    <div class="container d-flex flex-column flex-md-row justify-content-between">

        <div>
            <h5 class="fw-bold">Pythonškola</h5>
            <p class="text-white-50">Interaktívna platforma na učenie sa Pythonu.</p>
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
                    <li><a href="{{ auth()->user()->profile_url }}" class="text-white-50">Môj profil</a></li>
                @endauth
            </ul>
        </div>

        <div>
            <h6 class="fw-bold">Právne informácie</h6>
            <ul class="list-unstyled">
                <li><a href="{{ route('privacyPolicy') }}" class="text-white-50">Zásady ochrany osobných údajov</a>
                </li>
                <li><a href="{{ route('termsOfService') }}" class="text-white-50">Podmienky používania</a></li>
            </ul>
        </div>
    </div>

    <div class="text-center text-white-50 mt-4">
        <span class="fw-bold">© {{ date('Y') }} {{ env('APP_NAME') }}.</span> Všetky práva vyhradené.
    </div>
</footer>
