@extends('layouts.app')

@section('content')
    @php
        $blocksData = json_decode($lecture->blocks, true);
    @endphp
    @foreach ($blocksData['blocks'] as $block)
        @php
            $blockType = $block['type'];
            $blockData = $block['data'];
        @endphp

        @switch($blockType)
            {{-- Header --}}
            @case('header')
                @switch($blockData['level'])
                    @case(1)
                        <h1>{{ $blockData['text'] }}</h1>
                    @break

                    @case(2)
                        <h2>{{ $blockData['text'] }}</h2>
                    @break

                    @case(3)
                        <h3>{{ $blockData['text'] }}</h3>
                    @break

                    @case(4)
                        <h4>{{ $blockData['text'] }}</h4>
                    @break

                    @case(5)
                        <h5>{{ $blockData['text'] }}</h5>
                    @break

                    @case(6)
                        <h6>{{ $blockData['text'] }}</h6>
                    @break

                    @default
                @endswitch
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
                @php
                    $quizData = $blockData['questions'];
                    $questionNumber = 0;
                @endphp
                <quiz>
                    @foreach ($quizData['blocks'] as $quizBlock)
                        @php
                            $quizBlockType = $quizBlock['type'];
                            $quizBlockData = $quizBlock['data'];

                            $questionNumber++;
                        @endphp

                        @switch($quizBlockType)
                            {{-- Paragraph --}}
                            @case('paragraph')
                                <p>{{ $quizBlockData['text'] }}</p>
                            @break

                            {{-- One/multi choice --}}
                            @case('quizChoice')
                                <quiz-choice type="one-choice" :question-number="{{ $questionNumber }}">
                                    <template #question>
                                        {{ $quizBlockData['question'] }}
                                    </template>

                                    @php
                                        $answerId = 0;
                                    @endphp
                                    <template #answers>
                                        @foreach ($quizBlockData['answers'] as $answer)
                                            @php
                                                $answerId++;
                                            @endphp
                                            <quiz-answer :id="{{ $answerId }}" type="radio"
                                                :correct="@json($answer['correct'])">{{ $answer['answer'] }}</quiz-answer>
                                        @endforeach
                                    </template>

                                    <template #explanation>
                                        {{ $quizBlockData['explanation'] }}
                                    </template>
                                </quiz-choice>
                            @break

                            {{-- Drag and drop --}}
                            @case('quizDragAndDrop')
                                <quiz-drag-and-drop :question-number="{{ $questionNumber }}">
                                    <template #question>
                                        {{ $quizBlockData['question'] }}
                                    </template>

                                    <template #drags>
                                        @php
                                            $dropId = 0;
                                        @endphp
                                        @foreach ($quizBlockData['pairs'] as $pair)
                                            @php
                                                $dropId++;
                                            @endphp
                                            <quiz-drag :drop="{{ $dropId }}">
                                                {{ $pair['drag'] }}
                                            </quiz-drag>
                                        @endforeach
                                    </template>
                                    <template #drops>
                                        @php
                                            $id = 0;
                                        @endphp
                                        @foreach ($quizBlockData['pairs'] as $pair)
                                            @php
                                                $id++;
                                            @endphp
                                            <quiz-drop :id="{{ $id }}">
                                                {{ $pair['drop'] }}
                                            </quiz-drop>
                                        @endforeach
                                    </template>

                                    <template #explanation>
                                        {{ $quizBlockData['explanation'] }}
                                    </template>
                                </quiz-drag-and-drop>
                            @break

                            @default
                        @endswitch
                    @endforeach
                </quiz>
            @break

            @default
        @endswitch
    @endforeach
@endsection
