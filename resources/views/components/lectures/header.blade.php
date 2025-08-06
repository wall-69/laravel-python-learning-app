@props(['level'])

@switch($level)
    @case(1)
        <h1>{{ $slot }}</h1>
    @break

    @case(2)
        <h2>{{ $slot }}</h2>
    @break

    @case(3)
        <h3>{{ $slot }}</h3>
    @break

    @case(4)
        <h4>{{ $slot }}</h4>
    @break

    @case(5)
        <h5>{{ $slot }}</h5>
    @break

    @case(6)
        <h6>{{ $slot }}</h6>
    @break

    @default
@endswitch
