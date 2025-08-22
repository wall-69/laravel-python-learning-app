<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\ExerciseCompletion;
use App\Models\Quiz;
use App\Models\QuizCompletion;
use Illuminate\Http\Request;

class UserProgressController extends Controller
{
    public function completeQuiz(Request $request, Quiz $quiz)
    {
        $user = $request->user();

        // Check if quiz was already completed
        $alreadyCompleted = $user->completedQuizzes()->where("quiz_id", $quiz->id)->exists();

        if ($alreadyCompleted) {
            abort(403, "You already completed this quiz for points.");
        }

        // Add points to the user & mark quiz as complete
        $user->progress->addPoints(25);

        QuizCompletion::create([
            "user_id" => $user->id,
            "quiz_id" => $quiz->id
        ]);

        return response()->json([
            "message" => "Quiz was marked as complete."
        ]);
    }

    public function completeExercise(Request $request, Exercise $exercise)
    {
        $user = $request->user();

        // Check if exercise was already completed
        $alreadyCompleted = $user->completedExercises()->where("exercise_id", $exercise->id)->exists();

        if ($alreadyCompleted) {
            abort(403, "You already completed this exercise for points.");
        }

        // Add points to the user & mark exercise as complete
        $user->progress->addPoints(35);

        ExerciseCompletion::create([
            "user_id" => $user->id,
            "exercise_id" => $exercise->id
        ]);

        return response()->json([
            "message" => "Exercise was marked as complete."
        ]);
    }
}
