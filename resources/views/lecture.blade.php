@extends('layouts.app')

@section('content')
    <div class="container py-3">
        {{-- Title --}}
        <h1 class="text-center text-primary p-1 rounded-3">
            {{ $lecture->title }}
        </h1>

        @foreach ($lecture->blocks->blocks as $block)
            <x-lectures.render-block :block="$block" />
        @endforeach
        @if ($nextLecture)
            <a href="{{ route('lectures.show', $nextLecture) }}"
                class="btn btn-success d-flex align-items-center justify-content-center ms-auto mt-3 fw-bold"
                style="width: fit-content">
                {{ $nextLecture->category_order }}. {{ $nextLecture->title }}
                <i class="bx bxs-arrow-right-stroke bx-lg"></i>
            </a>
        @endif
    </div>
@endsection
