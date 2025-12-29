@props(['blockData', 'faqIdx'])

<div id="faq-{{ $faqIdx }}" class="accordion" style="max-width: 600px;">
    @foreach ($blockData->questions as $index => $item)
        @php
            $questionId = 'faq-' . $faqIdx . '-item-' . ($index + 1);
        @endphp

        <div class="accordion-item" style="border-radius: 0 !important;">
            <button class="accordion-button collapsed" style="box-shadow: none !important;" type="button"
                data-bs-toggle="collapse" data-bs-target="#{{ $questionId }}" aria-expanded="false"
                aria-controls="{{ $questionId }}">
                {!! $item->question !!}
            </button>
            <div id="{{ $questionId }}" class="accordion-collapse collapse" data-bs-parent="#faq-{{ $faqIdx }}">
                <div class="accordion-body">
                    {!! $item->answer !!}
                </div>
            </div>
        </div>
    @endforeach
</div>
