@extends('layouts.app')

@php
    $blocksData = json_decode($lecture->blocks, true);
@endphp

@section('content')
    @foreach ($blocksData['blocks'] as $block)
        @php
            $blockType = $block['type'];
            $blockData = $block['data'];
        @endphp

        @switch($blockType)
            {{-- Header --}}
            @case('header')
                <x-lectures.header :level="$blockData['level']">
                    {!! $blockData['text'] !!}
                </x-lectures.header>
            @break

            {{-- Paragraph --}}
            @case('paragraph')
                <p>{!! $blockData['text'] !!}</p>
            @break

            {{-- Delimiter --}}
            @case('delimiter')
                <hr class="mx-auto opacity-75 rounded-pill"
                    style="width: {{ $blockData['lineWidth'] }}%; height: {{ $blockData['lineThickness'] }}px; border: none; color: #000; background-color: #000;">
            @break

            {{-- Warning --}}
            @case('warning')
                <div class="bg-warning rounded-3 px-3 py-2 w-50" style="">
                    <span class="fw-bold d-flex gap-2 mb-1">
                        <i class="bx bx-alert-triangle bx-md align-self-start"></i> {{ $blockData['title'] }}
                    </span>

                    <p class="mb-0">
                        {{ $blockData['message'] }}
                    </p>
                </div>
            @break

            @case('list')
                <x-lectures.list :block-data="$blockData" />
            @break

            {{-- Code runner --}}
            @case('codeRunner')
                <code-runner>
                    <template #header>
                        {!! $blockData['header'] !!}
                    </template>
                    <template #description>
                        {!! $blockData['description'] !!}
                    </template>

                    <template #code>
                        <pre>{{ $blockData['code'] }}</pre>
                    </template>
                </code-runner>
            @break

            {{-- Code block --}}
            @case('codeBlock')
                <code-block>
                    <template #header>
                        {!! $blockData['header'] !!}
                    </template>
                    <template #description>
                        {!! $blockData['description'] !!}
                    </template>

                    <template #code>
                        <pre>{{ $blockData['code'] }}</pre>
                    </template>
                </code-block>
            @break

            {{-- Quiz --}}
            @case('quiz')
                <x-lectures.quiz :quiz-id="$block['id']" :quiz-data="$blockData['questions']"></x-lectures.quiz>
            @break

            {{-- Exercise --}}
            @case('exercise')
                <exercise id="{{ $block['id'] }}">
                    <template #header>
                        {!! $blockData['header'] !!}
                    </template>

                    <template #description>
                        {!! $blockData['description'] !!}
                    </template>

                    <template #assignment>
                        {!! $blockData['assignment'] !!}
                    </template>

                    <template #code>
                        <pre>{{ $blockData['code'] }}</pre>
                    </template>
                </exercise>
            @break

            @default
        @endswitch
    @endforeach
@endsection
