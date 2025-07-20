@extends('layouts.auth')

@section('form')
    <form action="{{ route('users.store') }}" method="POST" class="mw-form m-auto p-3 border">
        @csrf
        @method('POST')

        <h3 class="text-center text-primary mb-3">
            Registrovať sa
        </h3>

        <div class="row">
            <div class="col">
                {{-- First name --}}
                <div class="form-floating mb-3">
                    <input type="text" name="first_name" id="firstNameInput"
                        class="form-control @error('first_name') is-invalid @enderror" placeholder="Peter"
                        value="{{ old('first_name') }}" required>
                    <label for="firstNameInput">Meno</label>

                    @error('first_name')
                        <span class="text-danger mt-1">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            <div class="col">
                {{-- Last name --}}
                <div class="form-floating mb-3">
                    <input type="text" name="last_name" id="lastNameInput"
                        class="form-control @error('last_name') is-invalid @enderror" placeholder="Novák"
                        value="{{ old('last_name') }}" required>
                    <label for="lastNameInput">Priezvisko</label>

                    @error('last_name')
                        <span class="text-danger mt-1">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>
        </div>

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

        <div class="row">
            <div class="col">
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

            </div>
            <div class="col">
                {{-- Password confirmation --}}
                <div class="form-floating mb-3">
                    <input type="password" name="password_confirmation" id="passwordConfirmationInput"
                        class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="" required>
                    <label for="passwordConfirmationInput">Potvrďte heslo</label>

                    @error('password_confirmation')
                        <span class="text-danger mt-1">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

            </div>
        </div>


        {{-- TOS --}}
        <div class="form-check text-start mb-3">
            <input type="checkbox" name="tos" id="tosInput" class="form-check-input @error('tos') is-invalid @enderror"
                required>
            <label class="form-check-label" for="tosInput">
                Súhlasím s podmienkami používania
            </label>
        </div>

        {{-- Sign in --}}
        <div class="text-center">
            <button type="submit" class="btn btn-primary py-2">
                Registrovať sa
            </button>
        </div>

        <hr class="w-75 mx-auto">

        <p>
            Už máte účet? <a href="{{ route('login') }}" class="link-primary">Prihláste sa tu</a>.
        </p>
    </form>
@endsection
