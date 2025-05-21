<?php

namespace App\Http\Controllers\Api;

use App\Events\CategoryCreated;
use App\Events\CategoryDeleted;
use App\Events\CategoryUpdated;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return Category::with('courses')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = Category::create($data);

        event(new CategoryCreated($category));

        return response()->json($category, 201);
    }

    public function show(Category $category)
    {
        return $category->load('courses');
    }


    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category->update($data);

        event(new CategoryUpdated($category));

        return response()->json($category);
    }

    public function destroy(Category $category)
    {
        $categoryData = [
            'id' => $category->id,
            'name' => $category->name,
        ];

        $category->delete();

        event(new \App\Events\CategoryDeleted($categoryData));

        return response()->json(null, 204);
    }



}
