@props(['quizId', 'quizData', 'isComplete'])

<quiz id="{{ $quizId }}">
    <template #header>
        Kvíz: {!! $quizData->header !!}
    </template>

    @php
        $qNum = 0;
    @endphp
    @foreach ($quizData->questions->blocks as $quizBlock)
        @php
            $type = $quizBlock->type;
            $data = $quizBlock->data;
        @endphp

        @switch($type)
            {{-- Paragraph --}}
            @case('paragraph')
                <p>{!! $data->text !!}</p>
            @break

            {{-- One/multi choice --}}
            @case('quizChoice')
                @php
                    $correctCount = collect($data->answers)->where('correct', true)->count();

                    $inputType = $correctCount == 1 ? 'radio' : 'checkbox';
                @endphp

                <quiz-choice :question-number="{{ $qNum++ }}">
                    <template #question>
                        {!! $data->question !!}
                    </template>

                    <template #answers>
                        @foreach ($data->answers as $answerId => $answer)
                            <quiz-answer :id="{{ $answerId }}" type="{{ $inputType }}"
                                :correct="@json($answer->correct)">
                                {!! $answer->answer !!}
                            </quiz-answer>
                        @endforeach
                    </template>

                    <template #explanation>
                        {!! $data->explanation !!}
                    </template>
                </quiz-choice>
            @break

            {{-- Drag and drop --}}
            @case('quizDragAndDrop')
                <quiz-drag-and-drop :question-number="{{ $qNum++ }}">
                    <template #question>
                        {!! $data->question !!}
                    </template>

                    <template #drags>
                        @foreach ($data->pairs as $dropId => $pair)
                            <quiz-drag :drop="{{ $dropId }}">
                                {!! $pair->drag !!}
                            </quiz-drag>
                        @endforeach
                    </template>
                    <template #drops>
                        @foreach ($data->pairs as $dropId => $pair)
                            <quiz-drop :id="{{ $dropId }}">
                                {!! $pair->drop !!}
                            </quiz-drop>
                        @endforeach
                    </template>

                    @if (!empty($data->explanation))
                        <template #explanation>
                            {!! $data->explanation !!}
                        </template>
                    @endif
                </quiz-drag-and-drop>
            @break

            {{-- Code block --}}
            @case('codeBlock')
                <code-block>
                    @if (!empty($data->header))
                        <template #header>
                            {!! $data->header !!}
                        </template>
                    @endif
                    @if (!empty($data->description))
                        <template #description>
                            {!! $data->description !!}
                        </template>
                    @endif
                    <template #code>
                        <pre>{{ $data->code }}</pre>
                    </template>
                </code-block>
            @break
        @endswitch
    @endforeach
</quiz>
