<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Str;

class CategoryController extends Controller
{
    // GET

    public function index(Request $request)
    {
        $paginator = Category::search($request->get("search") ?? "")->paginate(10);

        return view("admin.categories.index", [
            "categories" => $paginator,
        ]);
    }

    public function create()
    {
        return view("admin.categories.create");
    }

    public function edit(Category $category)
    {
        return view("admin.categories.edit", [
            "category" => $category
        ]);
    }

    // POST

    public function store(CategoryRequest $request)
    {
        $data = $request->validated();

        Category::create($data);

        return redirect(route("admin.categories"))
            ->with("success", "Kategória bola úspešne vytvorená.");
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $data = $request->validated();

        $category->update($data);

        return redirect(route("admin.categories"))
            ->with("success", "Kategória bola úspešne upravená.");
    }

    public function destroy(Request $request, Category $category)
    {
        $category->delete();

        return redirect(route("admin.categories"))
            ->with("success", "Kategória bola úspešne odstránená.");
    }
}
