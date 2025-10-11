@extends('layouts.app')

@section('content')
    <h1 class="mb-3 text-center">Zoznam kvízov</h1>

    @foreach ($lectures as $lecture)
        <h2>Lekcia {{ $lecture->title }}</h2>

        <table class="table table-bordered table-striped">
            <colgroup>
                <col style="width: 90px;">
                <col>
            </colgroup>
            <thead>
                <tr>
                    <th class="text-center">Vyriešený</th>
                    <th>Názov</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lecture->quizzes as $nth => $quiz)
                    <tr onclick="window.location='{{ route('quizzes.show', $quiz) }}'" style="cursor: pointer;">
                        <td class="text-center">
                            @if (auth()->user()->completedQuizzes->contains('quiz_id', $quiz->id))
                                <i class="bx bx-check bx-md text-success align-middle"></i>
                            @else
                                <i class="bx bx-x bx-md text-danger align-middle"></i>
                            @endif
                        </td>
                        <td>
                            {{ $quiz->header }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
@endsection
