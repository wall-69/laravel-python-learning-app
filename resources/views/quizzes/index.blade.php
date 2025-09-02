@extends('layouts.app')

@section('content')
    <h1 class="mb-3 text-center">Zoznam kvízov</h1>

    @foreach ($lectures as $lecture)
        <h2>{{ $lecture->title }}</h2>

        <ol>
            @foreach ($lecture->quizzes as $nth => $quiz)
                <li>
                    <a href="{{ route('quizzes.show', $quiz) }}">Kvíz č. {{ $nth + 1 }}</a>
                </li>
            @endforeach
        </ol>
    @endforeach
@endsection
