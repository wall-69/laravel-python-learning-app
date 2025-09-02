@extends('layouts.app')

@section('content')
    <a href="{{ route('exercises.index') }}"
        class="btn btn-sm btn-secondary d-flex justify-content-center align-items-center gap-1 mb-3"
        style="max-width: max-content">
        <i class="bx bx-arrow-left bx-md"></i> Ísť späť
    </a>

    <exercise id="{{ $block['id'] }}">
        <template #header>
            {!! $block['data']['header'] !!}
        </template>

        <template #description>
            {!! $block['data']['description'] !!}
        </template>

        <template #assignment>
            {!! $block['data']['assignment'] !!}
        </template>

        <template #code>
            <pre>{{ $block['data']['code'] }}</pre>
        </template>
    </exercise>
@endsection
