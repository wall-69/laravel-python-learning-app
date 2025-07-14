@extends('layouts.app')

@section('content')
    <h1>
        Čo je to Python?
    </h1>

    <quiz class="mb-5">
        <!-- One choice -->
        <quiz-choice type="one-choice" :question-number="1">
            <template #question>
                Koľko rokov som mal, keď som mal 14?
            </template>

            <template #answers>
                <quiz-answer :id="1" type="radio"> 15 </quiz-answer>
                <quiz-answer :id="2" type="radio" :correct="true">
                    14
                </quiz-answer>
                <quiz-answer :id="3" type="radio"> 18 </quiz-answer>
            </template>

            <template #explanation>
                Keď som mal 14 rokov, tak som mohol mať jedine 14 rokov a
                nie 15 a ani 18, jedine, že by som klamal.
            </template>
        </quiz-choice>
        <!-- Multiple choice -->
        <quiz-choice type="multiple-choice" :question-number="2">
            <template #question>
                Ako prezývajú psov? (2 správne odpovede)
            </template>

            <template #answers>
                <quiz-answer :id="1" type="checkbox"> mačky </quiz-answer>
                <quiz-answer :id="2" type="checkbox" :correct="true">
                    psy
                </quiz-answer>
                <quiz-answer :id="3" type="checkbox" :correct="true">
                    pesovia
                </quiz-answer>
                <quiz-answer :id="4" type="checkbox">
                    autobusy
                </quiz-answer>
            </template>

            <template #explanation>
                Psov sme vždy nazývali ako psy a v 2025 aj ako pesovia.
                Mačky sú iné zvieratá a autobusy majú 4 kolesá, ale nie 4
                nohy.
            </template>
        </quiz-choice>

        <!-- Drag & drop -->
    </quiz>

    <code-runner :id="1"></code-runner>
@endsection
