@extends('layouts.user')

@section('content')
    <div class="row g-5 g-sm-3 g-md-5">
        <div class="col-12 col-sm-6 col-lg-4 col-xxl-3">
            <div id="user-profile-card" class="card border-0 shadow-sm overflow-hidden">
                <div class="bg-primary d-flex justify-content-center align-items-end" style="height: 120px;">

                    <img src="/img/avatars/male.png" alt="Profilová fotka" class="rounded-circle"
                        style="width: 150px; height: 150px; transform: translateY(50%); object-fit: cover;">
                </div>

                <div class="card-body" style="padding-top: 85px;">
                    <div class="d-flex flex-column align-items-center text-center">
                        <h3 class="card-title mb-0">
                            {{ $user->first_name }} {{ $user->last_name }}
                        </h3>
                        <p class="text-muted mb-0">
                            Pripojil sa {{ $user->created_at->locale('sk')->isoFormat('D. MMMM, YYYY') }}.
                        </p>
                    </div>

                    <hr class="my-4">

                    <div class="row">
                        <div class="col-6 text-center">
                            <h5 class="mb-0">{{ $user->progress->level }}</h5>
                            <small class="text-muted">Level</small>
                        </div>
                        <div class="col-6 text-center">
                            <h5 class="mb-0">{{ $user->progress->points }}</h5>
                            <small class="text-muted">XP bodov</small>
                        </div>
                    </div>

                    @auth
                        @if ($user->id == auth()->user()->id)
                            <a href="{{ route('users.settings') }}" class="btn btn-brand-blue w-100 mt-4">Nastavenia</a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-8 col-xxl-9">
            <div>
                <h2>Nedávna aktivita</h2>
                <hr>
                @if ($latestActivity->isEmpty())
                    <p>Zatiaľ žiadna aktivita.</p>
                @else
                    <div class="d-flex flex-column gap-3">
                        @foreach ($latestActivity as $activity)
                            <div class="d-flex gap-3 align-items-start">
                                <span class="position-relative d-inline-flex">
                                    @if ($activity->quiz_id)
                                        <i class="bx bxs-education bx-md text-bg-primary p-2 rounded-pill"></i>
                                    @else
                                        <i class="bx bxs-keyboard bx-md text-bg-primary p-2 rounded-pill"></i>
                                    @endif

                                    @unless ($loop->last)
                                        <span class="activity-line position-absolute bg-primary start-50 translate-middle-x"
                                            style="top: 100%; width: 4px; height: 32px;"></span>
                                    @endunless
                                </span>

                                <div>
                                    <p class="mb-1 h5">
                                        @php
                                            $activityName = $activity->type == 'quiz' ? 'kvíz' : 'cvičenie';
                                            $activityTitle =
                                                $activity->type == 'quiz' ? $activity->header : $activity->header;
                                            $activityRoute =
                                                $activity->type == 'quiz'
                                                    ? route('quizzes.show', $activity->quiz_id)
                                                    : route('exercises.show', $activity->exercise_id);
                                        @endphp

                                        Úspešne vyriešil {{ $activityName }}: <a
                                            href="{{ $activityRoute }}">{{ $activityTitle }}</a>.
                                    </p>
                                    <p class="mb-0 text-muted small">
                                        {{ ucfirst($activity->created_at->locale('sk')->diffForHumans()) }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
