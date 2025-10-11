@extends('layouts.app')

@section('content')
    <h1 class="mb-3 text-center">Zoznam cvičení</h1>

    @foreach ($lectures as $lecture)
        <h2>Lekcia {{ $lecture->title }}</h2>

        <table class="table table-bordered table-striped">
            <colgroup>
                <col style="width: 90px;">
                <col>
            </colgroup>
            <thead>
                <tr>
                    <th class="text-center">Vyriešené</th>
                    <th>Názov</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lecture->exercises as $nth => $exercise)
                    <tr onclick="window.location='{{ route('exercises.show', $exercise) }}'" style="cursor: pointer;">
                        <td class="text-center">
                            @if (auth()->user()->completedExercises->contains('exercise_id', $exercise->id))
                                <i class="bx bx-check bx-md text-success align-middle"></i>
                            @else
                                <i class="bx bx-x bx-md text-danger align-middle"></i>
                            @endif
                        </td>
                        <td>
                            {{ $exercise->header }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
@endsection
