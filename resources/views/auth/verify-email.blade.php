@extends('layouts.auth')

@section('form')
    <div class="mw-form m-auto p-3 border text-center">
        <h2>Overte svoju e-mailovú adresu.</h2>
        <p>
            Pre dokončenie registrácie musíte overiť svoju e-mailovú adresu.
            <br>
            Po overení e-mailu vám bude tiež sprístupnený náš online spúšťač kódu.
        </p>

        <form action="{{ route('verification.send') }}" method="POST">
            @csrf
            @method('POST')
            <span>
                Ak vám e-mail neprišiel, <button type="submit" class="link-primary border-0 bg-transparent px-0">
                    kliknutím sem vám ho pošleme znovu</button>.
            </span>
        </form>
    </div>
@endsection
