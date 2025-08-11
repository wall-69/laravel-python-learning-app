<template>
    <form @submit="checkQuiz" class="border p-3 d-grid gap-3">
        <header class="border-bottom">
            <h2 class="fw-bold">Kvíz: over svoje vedomosti!</h2>
            <p>
                Ak získaš aspoň 90%, dostaneš
                <span class="text-success fw-bold">+25 BODOV</span>. Kvíz môžeš
                zopakovať koľkokrát len chceš.
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
                        Super! Ide ti to veľmi dobre a za tvoju snahu ti bolo
                        pripočítaných
                        <span class="fw-bold">+25 BODOV</span>. Len tak ďalej!
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
import { computed, provide, reactive, ref } from "vue";

// Define
const props = defineProps({
    id: String,
});

// Variables
const questions = ref(null);
const results = ref(null);

const reveal = ref(false);
const questionMap = reactive({});

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

// Functions
function checkQuiz(event) {
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

    if (correctPercent.value >= 90) {
        // TODO: send quiz completion request
        console.log(props.id);
    }
}
</script>
