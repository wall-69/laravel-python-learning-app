@extends('layouts.user')

@section('content')
    <div class="d-none d-md-block">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="mb-0">
                {{ $user->first_name }} {{ $user->last_name }}
            </h1>

            @auth
                @if ($user->id == auth()->user()->id)
                    <a href="{{ route('users.settings') }}" class="btn btn-secondary">Nastavenia</a>
                @endif
            @endauth
        </div>
    </div>
    <div class="d-flex flex-column d-md-none">
        <a href="{{ route('users.settings') }}" class="btn btn-secondary align-self-end">Nastavenia</a>

        <h1 class="mb-0 mt-3">
            {{ $user->first_name }} {{ $user->last_name }}
        </h1>
    </div>
    <p>
        Pripojil sa {{ $user->created_at->locale('sk')->isoFormat('D. MMMM, YYYY') }}. <br>
    </p>

    <div class="progress user-progress" style="height: 24px">
        <div class="progress-bar fw-bold" style="min-width: 5em; width: {{ $user->progress->points }}%">
            Level {{ $user->progress->level }}
        </div>
    </div>


    {{-- Latest activity --}}
    <h2 class="mb-0 mt-3">
        Posledná aktivita
    </h2>

    @if ($latestActivity->isEmpty())
        <p>Zatiaľ žiadna aktivita.</p>
    @else
        <table class="table table-striped">
            <tbody class="table-group-divider">
                @foreach ($latestActivity as $activity)
                    <tr>
                        <td>
                            {{ $activity->created_at->locale('sk')->isoFormat('D. MMMM, YYYY') }}
                            &nbsp; &nbsp;
                            {{ $activity->quiz_id ? 'Splnil kvíz.' : 'Vyriešil cvičenie.' }}
                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    @endif
@endsection
