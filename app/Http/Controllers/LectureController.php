<?php

namespace App\Http\Controllers;

use App\Enums\LectureStatus;
use App\Http\Requests\LectureRequest;
use App\Models\Lecture;
use Illuminate\Http\Request;

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

        Lecture::create($data);

        return redirect(route("admin.lectures"))
            ->with("success", "Lekcia bola úspešne vytvorená.");
    }

    public function update(LectureRequest $request, Lecture $lecture)
    {
        $data = $request->validated();

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
