@props(['type', 'message'])

<div class="alert alert-{{ $type }} alert-dismissible fade show d-flex align-items-center gap-2"
    style="min-width: 320px;" role="alert">
    @if ($type == 'success')
        <i class="bx bxs-check-circle" style="font-size: 24px;"></i>
    @elseif($type == 'warning')
        <i class="bx bxs-alert-circle" style="font-size: 24px;"></i>
    @elseif($type == 'danger')
        <i class="bx bxs-fire" style="font-size: 24px;"></i>
    @endif

    {{ $message }}

    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
