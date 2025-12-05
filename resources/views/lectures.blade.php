@extends('layouts.app')

@section('content')
    <div class="container py-5">
        @foreach ($categories as $category)
            <section class="mb-5">
                <h2 class="fw-bold mb-2 d-flex align-items-center gap-3">
                    @if ($category->created_at >= now()->subMonth())
                        <span class="badge bg-warning text-dark">NOVINKA</span>
                    @endif
                    {{ $category->title }}
                </h2>

                <p class="text-muted mb-4">{{ $category->description }}</p>

                <div class="d-flex gap-4 flex-wrap justify-content-start">
                    @foreach ($category->lectures as $lecture)
                        <x-lecture-card :lecture="$lecture" />
                    @endforeach
                </div>
            </section>

            @unless ($loop->last)
                <hr class="my-5">
            @endunless
        @endforeach
    </div>
@endsection
