<template>
    <form @submit="checkQuiz" class="border p-3 mb-3 d-grid gap-3">
        <header class="border-bottom">
            <h2 class="fw-bold">Kvíz: over svoje vedomosti!</h2>
            <p v-if="!quizIsComplete">
                Ak získaš aspoň 90%, dostaneš
                <span class="text-success fw-bold">+25 BODOV</span>. Kvíz môžeš
                zopakovať koľkokrát len chceš.
            </p>
            <p v-else class="fst-italic">
                Tento kvíz si už úspešne vyriešil! Ak sa ti chce, tak si ho
                kľudne môžeš zopakovať.
            </p>
        </header>

        <!-- Quiz -->
        <ol ref="questions" class="d-grid gap-3">
            <slot></slot>
        </ol>

        <!-- Results -->
        <footer ref="results">
            <button type="submit" class="btn btn-primary mb-3">
                Ohodnotiť
            </button>

            <div
                v-show="reveal"
                class="px-3 py-2"
                :class="{
                    'text-bg-success bg-success': correctPercent >= 90,
                    'text-bg-warning bg-warning':
                        correctPercent >= 60 && correctPercent < 90,
                    'text-bg-danger bg-danger': correctPercent < 60,
                }"
            >
                <h3>Vyhodnotenie:</h3>
                <p class="mb-0">
                    <span class="fw-bold">
                        Získal si {{ correctPercent }}% !
                    </span>

                    <br />

                    <template v-if="correctPercent >= 90">
                        <template v-if="!quizIsComplete">
                            Super! Ide ti to veľmi dobre a za tvoju snahu ti
                            bolo pripočítaných
                            <span class="fw-bold">+25 BODOV</span>. Len tak
                            ďalej!
                        </template>
                        <template v-else>
                            Super! Ide ti to veľmi dobre. Len tak ďalej!
                        </template>
                    </template>
                    <template v-else-if="correctPercent >= 60">
                        Škoda, ale nie je to úplne márne. Skús si lekciu
                        preštudovať znova, poprípade si pozri správne
                        vysvetlenia pre lepšie pochopenie. Držíme ti palce!
                    </template>
                    <template v-else>
                        Fúha, niekde nastala chyba. Buď je táto lekcia zle
                        napísaná a vysvetlená, možno si ju len prefrčal a
                        nedával pozor alebo tvoje vedomosti zlyhávajú na nižších
                        úrovniach. V akomkoľvek prípade odporúčame si zopakovať
                        túto lekciu a zopakovať si test až si budeš istejší so
                        svojimi vedomosťami.
                    </template>
                </p>
            </div>
        </footer>
    </form>
</template>
<script setup>
import axios from "axios";
import { computed, inject, provide, reactive, ref } from "vue";
import { addAlert } from "../helpers";

// Define
const props = defineProps({
    id: String,
});

// Variables
const questions = ref(null);
const results = ref(null);

const reveal = ref(false);
const questionMap = reactive({});

const completedQuizzes = inject("completedQuizzes");

provide("reveal", reveal);
provide("questionMap", questionMap);

// Computed
const correct = computed(
    () => Object.values(questionMap).filter((v) => v === true).length
);
const total = computed(() => Object.values(questionMap).length);
const correctPercent = computed(() =>
    Math.round((correct.value / total.value) * 100)
);
const quizIsComplete = computed(() => completedQuizzes.includes(props.id));

// Functions
async function checkQuiz(event) {
    event.preventDefault();

    let qNum = 0;

    for (let question of questions.value.children) {
        let allCorrect = true;

        let answersContainer = question.querySelector(".answers");
        // This can be non quiz element such as a paragraph
        if (!answersContainer) {
            continue;
        }
        for (let answer of answersContainer.children) {
            if (answer.classList.contains("answer")) {
                const input = answer.querySelector("input");

                if (input.type == "radio" || input.type == "checkbox") {
                    const isChecked = input.checked;
                    const isCorrect = input.dataset.correct === "true";

                    if (
                        (isChecked && !isCorrect) ||
                        (!isChecked && isCorrect)
                    ) {
                        allCorrect = false;
                        break;
                    }
                }
            } else {
                for (let drag of answer.querySelectorAll(".drag")) {
                    if (drag.dataset.drop != drag.dataset.inDrop) {
                        allCorrect = false;
                    }
                }
            }
        }

        questionMap[qNum] = allCorrect;

        qNum++;
    }

    if (!reveal.value) {
        results.value.scrollIntoView();
    }
    reveal.value = true;

    if (correctPercent.value >= 90 && !quizIsComplete.value) {
        try {
            const response = await axios.post(
                "/user/progress/complete/quiz/" + props.id
            );

            if (response.status === 200) {
                completedQuizzes.push(props.id);

                addAlert(
                    "celebrate",
                    "Super! Tento kvíz si úspešne vyriešil. Gratulujeme!"
                );

                if (response.data.level_up) {
                    addAlert("level-up", response.data.level_up);
                }
            }
        } catch (error) {
            console.error("Marking quiz as complete failed: " + error);
        }
    }
}
</script>
