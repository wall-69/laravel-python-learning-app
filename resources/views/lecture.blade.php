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
                    {{ $blockData['text'] }}
                </x-lectures.header>
            @break

            {{-- Paragraph --}}
            @case('paragraph')
                <p>{{ $blockData['text'] }}</p>
            @break

            {{-- Code runner --}}
            @case('codeRunner')
                <code-runner>
                    <template #header>
                        {{ $blockData['header'] }}
                    </template>
                    <template #description>
                        {{ $blockData['description'] }}
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
                        {{ $blockData['header'] }}
                    </template>
                    <template #description>
                        {{ $blockData['description'] }}
                    </template>

                    <template #code>
                        <pre>{{ $blockData['code'] }}</pre>
                    </template>
                </code-block>
            @break

            {{-- Quiz --}}
            @case('quiz')
                <x-lectures.quiz :quiz-data="$blockData['questions']"></x-lectures.quiz>
            @break

            @default
        @endswitch
    @endforeach
@endsection
