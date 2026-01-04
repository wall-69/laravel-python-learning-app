<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>{{ env('APP_NAME') }}</title>

    <link href='https://cdn.boxicons.com/fonts/basic/boxicons.min.css' rel='stylesheet'>
    @vite(['resources/js/app.js'])

    @if (request()->routeIs('index'))
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    @endif
    <style>
        .list-group-numbered>.list-group-item::before {
            display: none;
        }
    </style>

    {{-- Chapters --}}
    @if (isset($isLecture) && $isLecture)
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                const container = document.querySelector("main .container");
                if (!container) return;

                const offcanvas = document.createElement("div");
                offcanvas.className = "offcanvas offcanvas-start";
                offcanvas.id = "lectureChaptersOffcanvas";
                offcanvas.tabIndex = -1;
                offcanvas.setAttribute("aria-labelledby", "lectureChaptersOffcanvasLabel");

                offcanvas.innerHTML = `
                    <div class="offcanvas-header">
                        <h5 id="lectureChaptersOffcanvasLabel">Kapitoly</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                `;

                const body = document.createElement("div");
                body.className = "offcanvas-body p-0";

                const list = document.createElement("div");
                list.className = "list-group list-group-flush list-group-numbered";

                // Track h2 numbering only
                let h2Counter = 0;
                const headings = container.querySelectorAll("h2");

                headings.forEach((heading, index) => {
                    if (!heading.id) heading.id = "heading-" + index;
                    heading.style.scrollMarginTop = "85px";

                    h2Counter++;
                    const title = `${h2Counter}. ${heading.textContent.trim()}`;

                    const a = document.createElement("a");
                    a.href = "#" + heading.id;
                    a.className = "list-group-item border-bottom px-2 py-1";
                    a.textContent = title;

                    list.appendChild(a);
                });

                body.appendChild(list);
                offcanvas.appendChild(body);
                document.body.appendChild(offcanvas);
            });
        </script>
    @endif

    {{-- Completed quizzes & exercises --}}
    <script>
        window.completedQuizzes = @json($completedQuizzes ?? []);
        window.completedExercises = @json($completedExercises ?? []);
    </script>
</head>

<body class="d-flex flex-column min-vh-100">
    <div id="app" class="flex-grow-1 d-flex flex-column">
        {{-- Alerts --}}
        <x-alerts />

        @if (!isset($hideHeader) || !$hideHeader)
            <x-header :lecture="$lecture ?? null" :category-lectures="$categoryLectures ?? null" />
        @endif

        <main class="flex-grow-1">
            @if (isset($categoryLectures))
                <div class="offcanvas offcanvas-start" tabindex="-1" id="categoryLecturesOffcanvas"
                    aria-labelledby="categoryLecturesOffcanvasLabel">
                    <div class="offcanvas-header">
                        <h5 id="categoryLecturesOffcanvasLabel">Lekcie</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body p-0">
                        <div class="list-group list-group-flush list-group-numbered">
                            @foreach ($categoryLectures as $categoryLecture)
                                <a href="{{ route('lectures.show', [$categoryLecture, $categoryLecture->slug]) }}"
                                    class="list-group-item border-bottom px-2 py-1 @if ($lecture->id == $categoryLecture->id) active @endif">
                                    {{ $categoryLecture->title }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- Side buttons for desktop --}}
            <div id="lecture-side-button-container" class="d-none d-md-flex">
                @if (isset($isLecture) && $isLecture)
                    <button id="lectureChaptersBtn" class="btn btn-secondary rounded-0" type="button"
                        data-bs-toggle="offcanvas" data-bs-target="#lectureChaptersOffcanvas"
                        aria-controls="lectureChaptersOffcanvas">
                        Kapitoly
                    </button>
                @endif

                @if (isset($categoryLectures))
                    <button class="btn btn-success rounded-0" type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#categoryLecturesOffcanvas" aria-controls="categoryLecturesOffcanvas">
                        {{ $lecture->category->title }}
                    </button>
                @endif
            </div>

            @yield('content')
        </main>

        <x-footer />
    </div>
</body>

</html>
