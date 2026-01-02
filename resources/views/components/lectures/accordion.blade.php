@props(['blockData', 'accordionIdx'])

<div id="accordion-{{ $accordionIdx }}" class="accordion">
    @foreach ($blockData->questions as $index => $item)
        @php
            $questionId = 'accordion-' . $accordionIdx . '-item-' . ($index + 1);
        @endphp

        <div class="accordion-item" style="border-radius: 0 !important;">
            <button class="accordion-button collapsed" style="box-shadow: none !important;" type="button"
                data-bs-toggle="collapse" data-bs-target="#{{ $questionId }}" aria-expanded="false"
                aria-controls="{{ $questionId }}">
                {!! $item->question !!}
            </button>
            <div id="{{ $questionId }}" class="accordion-collapse collapse"
                data-bs-parent="#accordion-{{ $accordionIdx }}">
                <div class="accordion-body">
                    {!! $item->answer !!}
                </div>
            </div>
        </div>
    @endforeach
</div>
