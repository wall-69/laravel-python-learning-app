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
        {{-- Drag and drop --}}
        <quiz-drag-and-drop :question-number="3">
            <template #question>
                Priraď pojmy k vysvetleniam.
            </template>

            <template #drags>
                <quiz-drag :drop="1">
                    Peking
                </quiz-drag>
                <quiz-drag :drop="2">
                    Washington DC
                </quiz-drag>
                <quiz-drag :drop="3">
                    Canberra
                </quiz-drag>
            </template>
            <template #drops>
                <quiz-drop :id="1">
                    Čína
                </quiz-drop>
                <quiz-drop :id="2">
                    USA
                </quiz-drop>
                <quiz-drop :id="3">
                    Austrália
                </quiz-drop>
            </template>

            <template #explanation>
                Hlavné mestá môžu byť nájdené na napríklad mapách. Tí, ktorí dávali pozor na geografii môžu potvrdiť, že
                toto sú správne odpovede.
            </template>
        </quiz-drag-and-drop>

        <quiz-drag-and-drop :question-number="4">
            <template #question>
                Priraď pojmy k vysvetleniam.
            </template>

            <template #drags>
                <quiz-drag :drop="1">
                    C++ je viacparadigmový programovací jazyk vyššej úrovne na všeobecné použitie, ktorý umožňuje pracovať
                    aj s prostriedkami nízkej úrovne. Má statickú typovú kontrolu, podporuje procedurálne programovanie,
                    dátovú abstrakciu, objektovo orientované programovanie, ale aj generické programovanie.

                </quiz-drag>
                <quiz-drag :drop="2">
                    Python je interpretovaný, interaktívny programovací jazyk, ktorý vytvoril Guido van Rossum, pôvodne ako
                    skriptovací jazyk pre Amoeba OS schopný systémových volaní. Python je často porovnávaný s jazykmi Tcl,
                    Perl, Scheme, Java a Ruby. Python je vyvíjaný ako open source projekt, a je v súčasnosti pri verzii 3.12
                </quiz-drag>
                <quiz-drag :drop="3">
                    JavaScript, je skriptovací programovací jazyk. Jazyk je používaný najmä pri tvorbe webových stránok.
                    Pôvodne ho vyvíjal Brendan Eich zo spoločnosti Netscape Communications pod názvom Mocha, neskôr pod
                    menom LiveScript.
                </quiz-drag>
            </template>
            <template #drops>
                <quiz-drop :id="1">
                    C++
                </quiz-drop>
                <quiz-drop :id="2">
                    Python
                </quiz-drop>
                <quiz-drop :id="3">
                    JavaScript
                </quiz-drop>
            </template>

            <template #explanation>
                Hlavné mestá môžu byť nájdené na napríklad mapách. Tí, ktorí dávali pozor na geografii môžu potvrdiť, že
                toto sú správne odpovede.
            </template>
        </quiz-drag-and-drop>
    </quiz>

    <code-runner class="mb-5"></code-runner>
    <code-runner class="mb-5"></code-runner>

    <code-block>
        <template #header>
            Príklad bloku kódu.
        </template>
        <template #description>
            Toto je popis bloku kódu, často bude obsahovať vysvetlivky ku kódu, ktorý sa nachádza nižšie.
        </template>
        <template #code>
            <pre>
                print("Hello!!")
        
                codeBlock = "Toto je code block"
                mojeCislo = 2
        
                print(codeBlock, mojeCislo)
        
                # Tu je podmienka
                if mojeCislo == 2:
                    print("They are two!!")
            </pre>
        </template>
    </code-block>
@endsection
