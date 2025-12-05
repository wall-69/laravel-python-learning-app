@extends('layouts.app')

@section('content')
    <div class="container py-3">
        {{-- Title --}}
        <h1 class="text-center text-primary p-1 rounded-3">
            {{ $lecture->title }}
        </h1>

        @foreach ($lecture->blocks->blocks as $block)
            @php
                $blockType = $block->type;
                $blockData = $block->data;
            @endphp
            @switch($blockType)
                {{-- Header --}}
                @case('header')
                    <x-lectures.header :level="$blockData->level">
                        {!! $blockData->text !!}
                    </x-lectures.header>
                @break

                {{-- Paragraph --}}
                @case('paragraph')
                    <p>{!! $blockData->text !!}</p>
                @break

                {{-- Delimiter --}}
                @case('delimiter')
                    <hr class="mx-auto opacity-75 rounded-pill"
                        style="width: {{ $blockData->lineWidth }}%; height: {{ $blockData->lineThickness }}px; border: none; color: #333; background-color: #333;">
                @break

                {{-- Warning --}}
                @case('warning')
                    <div class="lecture-warning bg-warning rounded-3 px-3 py-2">
                        <span class="fw-bold d-flex gap-2 mb-1">
                            <i class="bx bx-alert-triangle bx-md align-self-start"></i> {{ $blockData->title }}
                        </span>
                        <p class="mb-0">
                            {!! $blockData->message !!}
                        </p>
                    </div>
                @break

                {{-- List --}}
                @case('list')
                    <x-lectures.list :block-data="$blockData" />
                @break

                {{-- Image --}}
                @case('image')
                    <img src="{{ $blockData->file->url }}" alt="{{ $blockData->caption }}" class="lecture-image rounded-1 mb-3">
                @break

                {{-- Table --}}
                @case('table')
                    <div class="container table-responsive">
                        <table class="table table-striped table-bordered">
                            @if ($blockData->withHeadings)
                                <thead>
                                    <tr>
                                        @foreach ($blockData->content[0] as $headColumn)
                                            <th>{!! $headColumn !!}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                            @endif
                            <tbody class="table-group-divider">
                                @php
                                    $content = $blockData->content;
                                    $startIndex = $blockData->withHeadings ? 1 : 0;
                                @endphp
                                @for ($i = $startIndex; $i < count($content); $i++)
                                    <tr>
                                        @foreach ($content[$i] as $column)
                                            <td>{!! $column !!}</td>
                                        @endforeach
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                @break

                {{-- Code runner --}}
                @case('codeRunner')
                    <code-runner>
                        <template #header>
                            {!! $blockData->header !!}
                        </template>
                        <template #description>
                            {!! $blockData->description !!}
                        </template>
                        <template #code>
                            <pre>{{ $blockData->code }}</pre>
                        </template>
                    </code-runner>
                @break

                {{-- Code block --}}
                @case('codeBlock')
                    <code-block>
                        <template #header>
                            {!! $blockData->header !!}
                        </template>
                        <template #description>
                            {!! $blockData->description !!}
                        </template>
                        <template #code>
                            <pre>{{ $blockData->code }}</pre>
                        </template>
                    </code-block>
                @break

                {{-- Quiz --}}
                @case('quiz')
                    <x-lectures.quiz :quiz-id="$block->id" :quiz-data="$blockData"></x-lectures.quiz>
                @break

                {{-- Exercise --}}
                @case('exercise')
                    <exercise id="{{ $block->id }}">
                        <template #header>
                            {!! $blockData->header !!}
                        </template>
                        <template #description>
                            {!! $blockData->description !!}
                        </template>
                        <template #assignment>
                            {!! $blockData->assignment !!}
                        </template>
                        <template #code>
                            <pre>{{ $blockData->code }}</pre>
                        </template>
                    </exercise>
                @break

                @default
            @endswitch
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
