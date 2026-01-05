@extends('layouts.user')

@section('content')
    <h1>Nastavenia</h1>

    @if (!auth()->user()->hasVerifiedEmail())
        <div class="alert alert-danger" role="alert">
            <span class="fw-bold">Váš email nie je overený.</span>
            Prosím, skontrolujte svoju emailovú schránku a overte svoj email kliknutím na odkaz,
            ktorý sme vám
            poslali. Ak ste email neobdržali, môžete si nechať poslať nový
            <form class="d-inline" method="POST" action="{{ route('verification.send') }}">
                @csrf
                @method('POST')

                <button type="submit" class="link-primary border-0 bg-transparent px-0">kliknutím sem</button>.
            </form>
        </div>
    @endif

    <hr>

    <form action="{{ route('users.change-password', auth()->user()) }}" method="POST">
        @csrf
        @method('PATCH')

        <h2>Zmena hesla</h2>

        {{-- Old password --}}
        <div class="mb-3" style="max-width: 400px">
            <label for="oldPasswordInput" class="form-label">Staré heslo:</label>
            <input type="password" name="old_password" id="oldPasswordInput"
                class="form-control @error('old_password') is-invalid @enderror" placeholder="" required>

            @error('old_password')
                <span class="text-danger mt-1">
                    {{ $message }}
                </span>
            @enderror
        </div>

        {{-- Password --}}
        <div class="mb-3" style="max-width: 400px">
            <label for="passwordInput" class="form-label">Nové heslo:</label>
            <input type="password" name="password" id="passwordInput"
                class="form-control @error('password') is-invalid @enderror" placeholder="" required>

            @error('password')
                <span class="text-danger mt-1">
                    {{ $message }}
                </span>
            @enderror
        </div>

        {{-- Password confirmation --}}
        <div class="mb-3" style="max-width: 400px">
            <label for="passwordConfirmationInput" class="form-label">Potvrďte nové heslo:</label>
            <input type="password" name="password_confirmation" id="passwordConfirmationInput"
                class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="" required>

            @error('password_confirmation')
                <span class="text-danger mt-1">
                    {{ $message }}
                </span>
            @enderror
        </div>

        <button type="submit" class="btn btn-secondary">Zmeniť heslo</button>
    </form>

    <hr>

    <form action="{{ route('users.destroy', auth()->user()) }}" method="POST"
        onsubmit="return confirm('Ste si istý? Táto akcia je nenávratná!')">
        @csrf
        @method('DELETE')

        <h2>Vymazanie účtu</h2>
        <p>
            Vymazanie účtu je nenávratné. S vaším účtom budú taktiež vymazané všetky údaje, ako napríklad vyriešené testy,
            váš level, riešenia cvičení, atď.
        </p>

        {{-- Password --}}
        <div class="mb-3" style="max-width: 400px">
            <label for="deletePasswordInput" class="form-label">Heslo:</label>
            <input type="password" name="delete_password" id="deletePasswordInput"
                class="form-control @error('delete_password') is-invalid @enderror" placeholder="" required>

            @error('delete_password')
                <span class="text-danger mt-1">
                    {{ $message }}
                </span>
            @enderror
        </div>

        <button type="submit" class="btn btn-danger">
            Vymazať účet
        </button>
    </form>
@endsection
