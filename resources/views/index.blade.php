@extends('layouts.app')

@section('content')
    <h1>
        PyTutor
    </h1>

    <exercise>
        <template #header>
            Cvičenie č.1
        </template>

        <template #description>
            V tomto cvičení si skúsiš vytváranie a prácu s premennými.
        </template>

        <template #assignment>
            Vytvor premennú vek a nastav jej hodnotu na 17. Potom vytvor premennú min_vek, ktorej hodnota bude 18.
        </template>

        <template #tests>
        </template>
    </exercise>
@endsection
