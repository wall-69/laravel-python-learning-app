@props(['type', 'message'])

<div class="alert alert-{{ $type }} alert-dismissible fade show d-flex align-items-center gap-2"
    style="min-width: 320px;" role="alert">
    @if ($type == 'success')
        <i class="bx bx-md bxs-check-circle"></i>
    @elseif($type == 'warning')
        <i class="bx bx-md bxs-alert-circle"></i>
    @elseif($type == 'danger')
        <i class="bx bx-md bxs-fire"></i>
    @endif

    {{ $message }}

    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
