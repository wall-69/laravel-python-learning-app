@props(['lecture'])

<a href="{{ route('lectures.show', [$lecture, $lecture->slug]) }}" class="text-decoration-none text-dark">
    <div class="card shadow-sm border-0 h-100 lecture-card">
        <div class="ratio ratio-16x9">
            <img src="{{ asset($lecture->thumbnail) }}" alt="{{ $lecture->title }}" class="rounded-top object-fit-cover"
                style="object-position: center top;">
        </div>

        <div class="card-body d-flex flex-column">
            <h5 class="fw-bold mb-2">
                {{ $lecture->category_order }}.
                {{ $lecture->title }}
            </h5>

            <p class="text-muted flex-grow-1">
                {{ Str::limit($lecture->description, 150) }}
            </p>

            <div class="mt-3 d-flex align-items-center justify-content-between">
                <span class="text-primary fw-semibold">Čítať viac →</span>
            </div>
        </div>
    </div>
</a>
