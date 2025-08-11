<?php

namespace App\Http\Controllers;

use App\Enums\LectureStatus;
use App\Http\Requests\LectureRequest;
use App\Models\Lecture;
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
            "lectureStatuses" => LectureStatus::values()
        ]);
    }

    public function edit(Lecture $lecture)
    {
        return view("admin.lectures.edit", [
            "lecture" => $lecture,
            "lectureStatuses" => LectureStatus::values()
        ]);
    }

    public function show(Lecture $lecture)
    {
        return view("lecture", [
            "lecture" => $lecture
        ]);
    }

    // POST

    public function store(LectureRequest $request)
    {
        $data = $request->validated();

        $lecture = Lecture::create($data);

        // Set global block IDs
        $blocks = json_decode($data["blocks"], true);
        foreach ($blocks["blocks"] as &$block) {
            $block["id"] = "lecture_" . $lecture->id . "-" . Str::uuid();
        }
        $lecture->update(["blocks" => json_encode($blocks)]);

        return redirect(route("admin.lectures"))
            ->with("success", "Lekcia bola úspešne vytvorená.");
    }

    public function update(LectureRequest $request, Lecture $lecture)
    {
        $data = $request->validated();

        // Set global block IDs
        $blocks = json_decode($data["blocks"], true);
        $prefix = "lecture_" . $lecture->id . "-";
        foreach ($blocks["blocks"] as &$block) {
            if (!str_starts_with($block["id"], $prefix)) {
                $block["id"] = $prefix . Str::uuid();
            }
        }
        $data["blocks"] = json_encode($blocks);

        $lecture->update($data);

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
