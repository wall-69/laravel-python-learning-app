@extends('layouts.app')

@section('content')
    @foreach ($categories as $category)
        <h2 class="d-flex flex-wrap align-items-center gap-2">
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
                        <a href="{{ route('lectures.show', [$lecture, $lecture->slug]) }}" class="d-block">
                            <img src="{{ asset($lecture->thumbnail) }}" alt="{{ $lecture->title }}"
                                class="rounded-3 mb-3 w-100"
                                style="height: 16rem; object-fit: cover; object-position: top center;">
                        </a>
                        <h5 class="card-title">
                            <span class="fw-bold">{{ $lecture->category_order }}.</span>
                            <a href="{{ route('lectures.show', [$lecture, $lecture->slug]) }}" class="text-decoration-none">
                                {{ $lecture->title }}
                            </a>
                        </h5>

                        <p class="card-text overflow-y-scroll" style="flex: 1 1 auto; overflow-y: auto;">
                            {{ $lecture->description }}
                        </p>
                    </div>
                </article>
            @endforeach
        </div>

        @unless ($loop->last)
            <hr>
        @endunless
    @endforeach
@endsection
