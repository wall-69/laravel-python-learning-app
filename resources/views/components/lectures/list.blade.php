@props(['blockData'])

@switch($blockData->style ?? 'unordered')
    @case('unordered')
        <ul>
            @foreach ($blockData->items as $item)
                <li>
                    {!! $item->content !!}
                    @if (!empty($item->items))
                        <x-lectures.list :block-data="(object) ['style' => 'unordered', 'items' => $item->items]" />
                    @endif
                </li>
            @endforeach
        </ul>
    @break

    @case('ordered')
        @php
            $counterMap = [
                'numeric' => 'decimal',
                'lower-roman' => 'lower-roman',
                'upper-roman' => 'upper-roman',
                'lower-alpha' => 'lower-alpha',
                'upper-alpha' => 'upper-alpha',
            ];
            $listStyle = $counterMap[$blockData->meta->counterType ?? 'numeric'];
        @endphp
        <ol @if (!empty($blockData->meta->start)) start="{{ $blockData->meta->start }}" @endif
            style="list-style-type: {{ $listStyle }};">
            @foreach ($blockData->items as $item)
                <li>
                    {!! $item->content !!}
                    @if (!empty($item->items))
                        <x-lectures.list :block-data="(object) ['style' => 'ordered', 'items' => $item->items]" />
                    @endif
                </li>
            @endforeach
        </ol>
    @break

    @case('checklist')
        <ul>
            @foreach ($blockData->items as $item)
                <li>
                    <input type="checkbox" disabled @if (!empty($item->meta->checked)) checked @endif>
                    {!! $item->content !!}
                    @if (!empty($item->items))
                        <x-lectures.list :block-data="(object) ['style' => 'checklist', 'items' => $item->items]" />
                    @endif
                </li>
            @endforeach
        </ul>
    @break

@endswitch
