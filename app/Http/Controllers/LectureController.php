<?php

namespace App\Http\Controllers;

use App\Enums\LectureStatus;
use App\Http\Requests\LectureRequest;
use App\Models\Category;
use App\Models\Exercise;
use App\Models\Lecture;
use App\Models\Quiz;
use DB;
use Gate;
use Illuminate\Http\Request;
use Storage;
use Str;

class LectureController extends Controller
{
    // GET

    public function index(Request $request)
    {
        $categories = Category::with([
            "lectures" => function ($query) {
                $query->where("status", LectureStatus::PUBLIC->value)
                    ->orderBy("category_order");
            }
        ])->get();

        return view("lectures", [
            "hideSidebar" => true,
            "categories" => $categories,
        ]);
    }

    public function adminIndex(Request $request)
    {
        $paginator = Lecture::search($request->get("search") ?? "")->with("category")->paginate(10);

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
        Gate::authorize("show", [$lecture]);

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

        // Fill in the users code for completed exercises
        if ($user) {
            $completedExercises = $user->completedExercises->keyBy("exercise_id");
            $blocks = $lecture->blocks;

            foreach ($blocks->blocks as &$block) {
                if ($block->type == "exercise") {
                    $exerciseId = $block->id;

                    if ($completedExercises->has($exerciseId)) {
                        $block->data->code = $completedExercises->get($exerciseId)->code;
                    }
                }
            }

            $lecture->blocks = $blocks;
        }

        $nextLecture = $lecture->nextLecture();

        $completedQuizzes = $user?->completedQuizzes->pluck("quiz_id") ?? collect();
        $completedExercises = $user?->completedExercises->pluck("exercise_id") ?? collect();

        return view("lecture", [
            "lecture" => $lecture,
            "nextLecture" => $nextLecture,
            "categoryLectures" => $lecture->category->lectures,
            "completedQuizzes" => $completedQuizzes,
            "completedExercises" => $completedExercises,
            "isLecture" => true
        ]);
    }

    // POST

    public function store(LectureRequest $request)
    {
        $data = $request->validated();

        // Save thumbnail
        if ($request->file("thumbnail")) {
            $data["thumbnail"] = "storage/" . $request->file("thumbnail")->store("img/thumbnails", "public");
        }

        $lecture = Lecture::create($data);

        // Set global block IDs to quizzes and exercises
        // Also creates/updates the respective model
        $blocks = json_decode($data["blocks"], true);
        foreach ($blocks["blocks"] as &$block) {
            // Create Quiz model
            if ($block["type"] == "quiz") {
                $quiz = Quiz::create([
                    "lecture_id" => $lecture->id,
                    "block" => $block
                ]);

                $block["id"] = $quiz->id;

                $quiz->update([
                    "block" => $block
                ]);
            }
            // Create Exercise model
            else if ($block["type"] == "exercise") {
                $exercise = Exercise::create([
                    "lecture_id" => $lecture->id,
                    "block" => $block,
                    "tests" => str_replace(["&gt;", "&lt;"], [">", "<"], $block["data"]["tests"])
                ]);

                $block["id"] = $exercise->id;

                $exercise->update([
                    "block" => $block
                ]);
            }
        }
        $lecture->update(["blocks" => $blocks]);

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

        // Save thumbnail
        if ($request->file("thumbnail")) {
            Storage::delete(str_replace("storage/", "", $lecture->thumbnail));

            $data["thumbnail"] = "storage/" . $request->file("thumbnail")->store("img/thumbnails", "public");
        }

        // Set global block IDs to quizzes and exercises
        // Also creates/updates the respective model
        $blocks = json_decode($data["blocks"], true);
        foreach ($blocks["blocks"] as &$block) {
            if ($block["type"] == "quiz") {
                if (!Str::isUuid($block["id"]) || !Quiz::find($block["id"])) {
                    $quiz = Quiz::create([
                        "lecture_id" => $lecture->id,
                        "block" => $block
                    ]);

                    $block["id"] = $quiz->id;
                } else {
                    $quiz = Quiz::find($block["id"]);
                }

                $quiz->update([
                    "block" => $block
                ]);
            } else if ($block["type"] == "exercise") {
                if (!Str::isUuid($block["id"]) || !Exercise::find($block["id"])) {
                    $exercise = Exercise::create([
                        "lecture_id" => $lecture->id,
                        "block" => $block,
                        "tests" => str_replace(["&gt;", "&lt;"], [">", "<"], $block["data"]["tests"])
                    ]);

                    $block["id"] = $exercise->id;
                } else {
                    $exercise = Exercise::find($block["id"]);
                    $exercise->update([
                        "tests" => str_replace(["&gt;", "&lt;"], [">", "<"], $block["data"]["tests"])
                    ]);
                }

                $exercise->update([
                    "block" => $block
                ]);
            }
        }
        $data["blocks"] = $blocks;

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

    public function updateBlocks(Request $request, Lecture $lecture)
    {
        $request->validate([
            "blocks" => "required|string"
        ]);

        $blocks = json_decode($request->input("blocks"), true);

        $lecture->update(["blocks" => $blocks]);

        return response()->json(["success" => true]);
    }

    public function destroy(Request $request, Lecture $lecture)
    {
        Storage::disk("public")->delete(str_replace("storage/", "", $lecture->thumbnail));

        $lecture->delete();

        return redirect(route("admin.lectures"))
            ->with("success", "Lekcia bola úspešne odstránená.");
    }
}
