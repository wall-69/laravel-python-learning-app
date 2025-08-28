@php
    $alerts = array_filter([
        'success' => session('success'),
        'warning' => session('warning'),
        'danger' => session('danger'),
        'celebrate' => session('celebrate'),
        'level-up' => session('level_up'),
    ]);
@endphp

<alerts-container :alerts='@json($alerts)'></alerts-container>
