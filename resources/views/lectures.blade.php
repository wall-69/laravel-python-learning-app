@extends('layouts.app')

@section('content')
    <h1 class="mb-3 text-center">Zoznam lekcií</h1>

    @foreach ($categories as $category)
        <h2 class="d-flex align-items-center gap-2">
            @if ($category->created_at >= now()->subMonth())
                <span class="badge text-bg-warning">NOVINKA</span>
            @endif
            {{ $category->title }}
        </h2>
        <p>
            {{ $category->description }}
        </p>

        <div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-3 g-0">
            @foreach ($category->lectures as $lecture)
                <article class="card lecture-card">
                    <div class="card-body d-flex flex-column h-100">
                        <h5 class="card-title">
                            <span class="fw-bold">{{ $lecture->category_order }}.</span>
                            <a href="{{ route('lectures.show', [$lecture, $lecture->slug]) }}" class="link link-primary">
                                {{ $lecture->title }}
                            </a>
                        </h5>

                        <p class="card-text overflow-y-scroll" style="flex:1 1 auto; overflow-y:auto;">
                            {{ $lecture->description }}
                        </p>

                        <a href="{{ route('lectures.show', [$lecture, $lecture->slug]) }}"
                            class="mt-auto me-auto me-md-0 btn btn-primary">
                            Otvoriť
                        </a>
                    </div>
                </article>
            @endforeach
        </div>

        @unless ($loop->last)
            <hr>
        @endunless
    @endforeach
@endsection
