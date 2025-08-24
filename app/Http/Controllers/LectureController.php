<?php

namespace App\Http\Controllers;

use App\Enums\LectureStatus;
use App\Http\Requests\LectureRequest;
use App\Models\Category;
use App\Models\Exercise;
use App\Models\Lecture;
use App\Models\Quiz;
use DB;
use Illuminate\Http\Request;
use Str;

class LectureController extends Controller
{
    // GET

    public function index(Request $request)
    {
        $paginator = Lecture::search($request->get("search") ?? "")->paginate(10);

        return view("admin.lectures.index", [
            "lectures" => $paginator,
        ]);
    }

    public function create()
    {
        return view("admin.lectures.create", [
            "categories" => Category::all(),
            "lectureStatuses" => LectureStatus::values()
        ]);
    }

    public function edit(Lecture $lecture)
    {
        return view("admin.lectures.edit", [
            "lecture" => $lecture,
            "categories" => Category::all(),
            "lectureStatuses" => LectureStatus::values()
        ]);
    }

    public function show(Request $request, Lecture $lecture, ?string $slug = null)
    {
        // If the slug is not correct we redirect to the correct one
        if (!$slug || $lecture->slug != $slug) {
            return redirect()->route("lectures.show", [$lecture, $lecture->slug], 301);
        }

        // Count lecture views
        $viewedLectures = session("viewed_lectures", []);
        if (!isset($viewedLectures[$lecture->id])) {
            $viewedLectures[$lecture->id] = true;
            session(["viewed_lectures" => $viewedLectures]);

            $lecture->increment("views");
        }

        $user = $request->user();
        $completedQuizzes = $user?->completedQuizzes->pluck("quiz_id") ?? collect();
        $completedExercises = $user?->completedExercises->pluck("exercise_id") ?? collect();

        return view("lecture", [
            "lecture" => $lecture,
            "completedQuizzes" => $completedQuizzes,
            "completedExercises" => $completedExercises,
        ]);
    }

    // POST

    public function store(LectureRequest $request)
    {
        $data = $request->validated();

        $lecture = Lecture::create($data);

        // Set global block IDs to quizzes and exercises
        // Also creates/updates the respective model
        $blocks = json_decode($data["blocks"], true);
        foreach ($blocks["blocks"] as &$block) {
            // Create Quiz model
            if ($block["type"] == "quiz") {
                $quiz = Quiz::create([
                    "lecture_id" => $lecture->id
                ]);

                $block["id"] = $quiz->id;
            }
            // Create Exercise model
            else if ($block["type"] == "exercise") {
                $exercise = Exercise::create([
                    "lecture_id" => $lecture->id,
                    "tests" => $block["data"]["tests"]
                ]);

                $block["id"] = $exercise->id;
            }
        }
        $lecture->update(["blocks" => json_encode($blocks)]);

        // Category order
        DB::transaction(function () use ($data, $lecture) {
            $desiredOrder = $data["category_order"];
            $categoryId = $data["category_id"];

            // Recursive function to push conflicts
            $pushConflict = function ($order) use ($categoryId, &$pushConflict, $lecture) {
                $conflict = Lecture::where("category_id", $categoryId)
                    ->where("category_order", $order)
                    ->where("id", "!=", $lecture->id)
                    ->first();

                if ($conflict) {
                    $pushConflict($order + 1); // go deeper first
                    $conflict->update(["category_order" => $order + 1]); // update on return
                }
            };

            $pushConflict($desiredOrder);

            // Set the new lecture to the original desired order
            $lecture->update(["category_order" => $desiredOrder]);
        });

        return redirect(route("admin.lectures"))
            ->with("success", "Lekcia bola úspešne vytvorená.");
    }

    public function update(LectureRequest $request, Lecture $lecture)
    {
        $data = $request->validated();

        // Set global block IDs to quizzes and exercises
        // Also creates/updates the respective model
        $blocks = json_decode($data["blocks"], true);
        foreach ($blocks["blocks"] as &$block) {
            if ($block["type"] == "quiz") {
                if (!Str::isUuid($block["id"])) {
                    $quiz = Quiz::create([
                        "lecture_id" => $lecture->id
                    ]);

                    $block["id"] = $quiz->id;
                }
            } else if ($block["type"] == "exercise") {
                if (!Str::isUuid($block["id"])) {
                    $exercise = Exercise::create([
                        "lecture_id" => $lecture->id,
                        "tests" => $block["data"]["tests"]
                    ]);

                    $block["id"] = $exercise->id;
                } else {
                    $exercise = Exercise::find($block["id"]);
                    $exercise->update([
                        "tests" => $block["data"]["tests"]
                    ]);
                }
            }
        }
        $data["blocks"] = json_encode($blocks);

        DB::transaction(function () use ($data, $lecture, $blocks) {
            $desiredOrder = $data["category_order"];
            $categoryId = $data["category_id"];

            // Only run conflict shifting if order or category changed
            if ($lecture->category_order != $desiredOrder || $lecture->category_id != $categoryId) {
                $pushConflict = function ($order) use (&$pushConflict, $categoryId, $lecture) {
                    $conflict = Lecture::where("category_id", $categoryId)
                        ->where("category_order", $order)
                        ->where("id", "!=", $lecture->id)
                        ->first();

                    if ($conflict) {
                        $pushConflict($order + 1);
                        $conflict->update(["category_order" => $order + 1]);
                    }
                };

                $pushConflict($desiredOrder);

                $lecture->update([
                    "category_order" => $desiredOrder,
                    "category_id" => $categoryId,
                ]);
            }

            // Update other fields
            $lecture->update(collect($data)->except(["category_order", "category_id"])->toArray());

            // Delete removed quizzes
            $quizIds = collect($blocks["blocks"])->where("type", "quiz")->pluck("id");
            if ($quizIds->isEmpty()) {
                Quiz::where("lecture_id", $lecture->id)->delete();
            } else {
                Quiz::where("lecture_id", $lecture->id)
                    ->whereNotIn("id", $quizIds)
                    ->delete();
            }

            // Delete removed exercises
            $exerciseIds = collect($blocks["blocks"])->where("type", "exercise")->pluck("id");
            if ($exerciseIds->isEmpty()) {
                Exercise::where("lecture_id", $lecture->id)->delete();
            } else {
                Exercise::where("lecture_id", $lecture->id)
                    ->whereNotIn("id", $exerciseIds)
                    ->delete();
            }
        });


        return redirect(route("admin.lectures"))
            ->with("success", "Lekcia bola úspešne upravená.");
    }

    public function destroy(Request $request, Lecture $lecture)
    {
        $lecture->delete();

        return redirect(route("admin.lectures"))
            ->with("success", "Lekcia bola úspešne odstránená.");
    }
}
