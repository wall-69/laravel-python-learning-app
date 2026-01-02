@props(['blockData', 'accordionIdx'])

<div id="accordion-{{ $accordionIdx }}" class="accordion mb-3">
    @foreach ($blockData->questions as $index => $item)
        @php
            $questionId = 'accordion-' . $accordionIdx . '-item-' . ($index + 1);
        @endphp

        <div class="accordion-item" style="border-radius: 0 !important;">
            <div class="accordion-header" id="heading-{{ $questionId }}">
                <button class="accordion-button collapsed" style="box-shadow: none !important;" type="button"
                    data-bs-toggle="collapse" data-bs-target="#{{ $questionId }}" aria-expanded="false"
                    aria-controls="{{ $questionId }}">
                    {!! $item->question !!}
                </button>
            </div>
            <div id="{{ $questionId }}" class="accordion-collapse collapse"
                data-bs-parent="#accordion-{{ $accordionIdx }}">
                <div class="accordion-body">
                    @php
                        $answer = $item->answer;
                    @endphp

                    @if (is_iterable($answer))
                        @foreach ($answer as $innerBlock)
                            <x-lectures.render-block :block="$innerBlock" />
                        @endforeach
                    @else
                        {!! $answer !!}
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
