@extends('layouts.auth')

@section('form')
    <form action="{{ route('authenticate') }}" method="POST" class="mw-form m-auto p-3 border">
        @csrf
        @method('POST')

        <h3 class="text-center text-primary mb-3">
            Prihlásiť sa
        </h3>

        {{-- Email --}}
        <div class="form-floating mb-3">
            <input type="email" name="email" id="emailInput" class="form-control @error('email') is-invalid @enderror"
                placeholder="novak@gmail.com" value="{{ old('email') }}" required>
            <label for="emailInput">Email</label>

            @error('email')
                <span class="text-danger mt-1">
                    {{ $message }}
                </span>
            @enderror
        </div>

        {{-- Password --}}
        <div class="form-floating mb-3">
            <input type="password" name="password" id="passwordInput"
                class="form-control @error('password') is-invalid @enderror" placeholder="" required>
            <label for="passwordInput">Heslo</label>

            @error('password')
                <span class="text-danger mt-1">
                    {{ $message }}
                </span>
            @enderror
        </div>

        {{-- Remember me --}}
        <div class="form-check text-start mb-3">
            <input type="checkbox" name="remember_me" id="rememberMeInput" class="form-check-input">
            <label class="form-check-label" for="rememberMeInput">
                Zapamätať si ma
            </label>
        </div>

        {{-- Sign in --}}
        <div class="text-center">
            <button type="submit" class="btn btn-primary py-2">
                Prihlásiť sa
            </button>
        </div>

        <hr class="w-75 mx-auto">

        <p>
            Nemáte účet? <a href="{{ route('register') }}" class="link-primary">Zaregistrujte sa tu</a>.
        </p>
    </form>
@endsection
