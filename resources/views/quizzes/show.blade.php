@extends('layouts.app')

@section('content')
    <a href="{{ route('quizzes.index') }}"
        class="btn btn-sm btn-secondary d-flex justify-content-center align-items-center gap-1 mb-3"
        style="max-width: max-content">
        <i class="bx bx-arrow-left bx-md"></i> Ísť späť
    </a>

    @php
        $quizData = json_decode($quiz->block, true);
    @endphp
    <x-lectures.quiz :quiz-id="$quiz->id" :quiz-data="$quizData['data']"></x-lectures.quiz>
@endsection
