@extends('layouts.app')

@section('content')
    <h1>
        PyTutor
    </h1>

    <a href="{{ route('lectures.show', 'zaklady') }}">Lekcia</a>
@endsection
