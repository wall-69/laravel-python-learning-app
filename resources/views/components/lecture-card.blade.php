@props(['lecture'])

{{-- Horizontal --}}
<a href="{{ route('lectures.show', [$lecture, $lecture->slug]) }}" class="text-decoration-none text-dark"
    style="width:100%; max-width:900px;">
    <div class="card shadow-sm h-100 lecture-card border-brand-blue">
        <div class="row g-0 align-items-center">
            <div class="col-auto" style="width: 220px; height: 140px;">
                <img src="{{ asset($lecture->thumbnail) }}" alt="{{ $lecture->title }}"
                    class="img-fluid rounded-start object-fit-cover"
                    style="width: 220px; height: 140px; object-position: center top;">
            </div>

            <div class="col">
                <div class="card-body py-3">
                    <h5 class="fw-bold mb-1">
                        {{ $lecture->category_order }}. {{ $lecture->title }}
                    </h5>

                    <p class="text-muted mb-2">
                        {{ Str::limit($lecture->description, 160) }}
                    </p>

                    <div class="d-flex align-items-center justify-content-between mt-2">
                        <small class="text-muted">{{ $lecture->category->title }}</small>
                        <span class="text-primary fw-semibold read-more">Čítať viac →</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</a>

{{-- Vertical --}}
<a href="{{ route('lectures.show', [$lecture, $lecture->slug]) }}" class="text-decoration-none text-dark"
    style="width: 100%; max-width: 374px;">
    <div class="card shadow-sm h-100 lecture-card border-brand-blue">
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
                <span class="text-primary fw-semibold read-more">Čítať viac →</span>
            </div>
        </div>
    </div>
</a>
