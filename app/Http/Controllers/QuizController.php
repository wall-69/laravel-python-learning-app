<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    // GET

    public function index(Request $request)
    {
        $quizzes = Quiz::select("id", "lecture_id")->with('lecture:id,title')->get();

        // Group quizzes by lecture id
        $grouped = $quizzes->groupBy(fn($quiz) => $quiz->lecture->id);

        // Transform into a collection of lectures with quizzes
        $lectures = $grouped->map(function ($quizzes) {
            $lecture = $quizzes->first()->lecture;
            return (object)[
                "id" => $lecture->id,
                "title" => $lecture->title,
                "quizzes" => $quizzes
            ];
        })->values();

        return view("quizzes.index", [
            "hideSidebar" => true,
            "lectures" => $lectures,
        ]);
    }

    public function show(Request $request, Quiz $quiz)
    {
        $user = $request->user();

        $completedQuizzes = $user?->completedQuizzes->pluck("quiz_id") ?? collect();

        return view("quizzes.show", [
            "hideSidebar" => true,
            "completedQuizzes" => $completedQuizzes,
            "block" => json_decode($quiz->block, true),
        ]);
    }
}
