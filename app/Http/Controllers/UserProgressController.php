<?php

namespace App\Http\Controllers;

use App\Models\Lecture;
use App\Models\QuizCompletion;
use Illuminate\Http\Request;

class UserProgressController extends Controller
{
    public function completeQuiz(Request $request, string $quizId)
    {
        $user = $request->user();

        // Check if quiz was already completed
        $alreadyCompleted = $user->completedQuizzes()->where("quiz_id", $quizId)->exists();

        if ($alreadyCompleted) {
            abort(403, "You already completed this quiz for points.");
        }

        // Get the lecture id from the quiz id & get the lecture
        preg_match('/^lecture_([^-]+)/', $quizId, $matches);
        $lectureId = $matches[1] ?? null;
        $lecture = Lecture::find($lectureId);
        if (!$lecture) {
            abort(403, "Invalid lecture id.");
        }

        // Check if this quiz is the lecture
        $blocks = json_decode($lecture->blocks, true)["blocks"];
        $quizExists = collect($blocks)->contains(function ($block) use ($quizId) {
            return $block["type"] == "quiz" && $block["id"] == $quizId;
        });

        if (!$quizExists) {
            abort(403, "Invalid lecture quiz id.");
        }

        // Add points to the user & mark quiz as complete
        $user->progress->addPoints(20);

        QuizCompletion::create([
            "user_id" => $user->id,
            "quiz_id" => $quizId
        ]);

        return response()->json([
            "message" => "Quiz marked as completed."
        ]);
    }

    public function completeExercise(Request $request, string $exerciseId) {}
}
