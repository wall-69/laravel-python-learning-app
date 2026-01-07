@props(['block'])

@php
    $blockType = $block->type;
    $blockData = $block->data;

    $accordionIdx = 0;
@endphp
@switch($blockType)
    @case('header')
        <x-lectures.header :level="$blockData->level">
            {!! $blockData->text !!}
        </x-lectures.header>
    @break

    @case('paragraph')
        <p>{!! $blockData->text !!}</p>
    @break

    @case('delimiter')
        <hr class="mx-auto opacity-75 rounded-pill my-4"
            style="width: {{ $blockData->lineWidth }}%; height: {{ $blockData->lineThickness }}px; border: none; color: #000; background-color: #000;">
    @break

    @case('warning')
        <div class="lecture-warning bg-warning rounded-3 px-3 py-2 mb-3">
            <span class="fw-bold d-flex gap-2 mb-1">
                <i class="bx bx-alert-triangle bx-md align-self-start"></i> {{ $blockData->title }}
            </span>
            <p class="mb-0">
                {!! $blockData->message !!}
            </p>
        </div>
    @break

    @case('list')
        <x-lectures.list :block-data="$blockData" />
    @break

    @case('image')
        <img src="{{ $blockData->file->url }}" alt="{{ $blockData->caption }}" class="lecture-image rounded-1 mb-3">
    @break

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

    @case('codeRunner')
        <code-runner>
            @if (!empty($blockData->header))
                <template #header>
                    {!! $blockData->header !!}
                </template>
            @endif
            @if (!empty($blockData->description))
                <template #description>
                    {!! $blockData->description !!}
                </template>
            @endif
            <template #code>
                <pre>{{ $blockData->code }}</pre>
            </template>
        </code-runner>
    @break

    @case('codeBlock')
        <code-block class="mb-3">
            @if (!empty($blockData->header))
                <template #header>
                    {!! $blockData->header !!}
                </template>
            @endif
            @if (!empty($blockData->description))
                <template #description>
                    {!! $blockData->description !!}
                </template>
            @endif
            <template #code>
                <pre>{{ $blockData->code }}</pre>
            </template>
        </code-block>
    @break

    @case('quiz')
        <x-lectures.quiz :quiz-id="$block->id" :quiz-data="$blockData"></x-lectures.quiz>
    @break

    @case('exercise')
        <exercise id="{{ $block->id }}">
            <template #header>
                Cvičenie: {!! $blockData->header !!}
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

    @case('revision')
        <x-lectures.revision :block-data="$blockData"></x-lectures.revision>
    @break

    @case('accordion')
        @php
            $accordionIdx++;
        @endphp

        <x-lectures.accordion :block-data="$blockData" :accordion-idx="$accordionIdx"></x-lectures.accordion>
    @break

    @case('columns')
        @php
            $cols = is_array($blockData->cols)
                ? $blockData->cols
                : (is_object($blockData->cols)
                    ? (array) $blockData->cols
                    : []);
            $colsCount = count($cols);
            if ($colsCount < 1) {
                $colsCount = 1;
            }
            if ($colsCount > 3) {
                $colsCount = 3;
            }
            $colClass = (int) floor(12 / $colsCount);
        @endphp
        <div class="row gx-3 gy-3 mb-3">
            @foreach (array_slice($cols, 0, 3) as $col)
                <div class="col-12 col-md-{{ $colClass }}">
                    @if (!empty($col->blocks))
                        @foreach ($col->blocks as $innerBlock)
                            <x-lectures.render-block :block="$innerBlock" />
                        @endforeach
                    @endif
                </div>
            @endforeach
        </div>
    @break

    @default
@endswitch
