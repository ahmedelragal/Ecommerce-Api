<?php

namespace App\Http\Controllers\Api\Product;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductManagement\CategoryStoreRequest;
use App\Http\Requests\ProductManagement\CategoryUpdateRequest;

class CategoryController extends Controller
{
    public function index()
    {
        return Category::all();
    }

    public function store(CategoryStoreRequest $request)
    {
        $category = Category::create($request->only('name'));
        return response()->json($category, 201);
    }

    public function show($id)
    {
        $category = Category::find($id);
        if ($category) {
            return response()->json($category, 200);
        } else {
            return response()->json(['message' => 'Category not found'], 404);
        }
    }

    public function update(CategoryUpdateRequest $request, $id)
    {
        $category = Category::find($id);
        if ($category) {
            $category->update($request->only('name'));
            return response()->json($category, 200);
        } else {
            return response()->json(['message' => 'Category not found'], 404);
        }
    }

    public function destroy($id)
    {
        $category = Category::find($id);
        if ($category) {
            if ($category->delete()) {
                return response()->json(null, 204);
            }
        } else {
            return response()->json(['message' => 'Category not found'], 404);
        }
    }
}
