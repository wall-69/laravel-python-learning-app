@extends('layouts.app')

@section('content')
    <h1 class="mb-3 text-center">Zoznam cvičení</h1>

    @foreach ($lectures as $lecture)
        <h2>{{ $lecture->title }}</h2>

        <ol>
            @foreach ($lecture->exercises as $nth => $exercise)
                <li>
                    <a href="{{ route('exercises.show', $exercise) }}">{{ $exercise->header }}</a>
                </li>
            @endforeach
        </ol>
    @endforeach
@endsection
