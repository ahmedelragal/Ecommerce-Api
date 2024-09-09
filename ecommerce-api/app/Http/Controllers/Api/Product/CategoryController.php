<?php

namespace App\Http\Controllers\Api\Product;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductManagement\CategoryStoreRequest;
use App\Http\Requests\ProductManagement\CategoryUpdateRequest;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Cache::remember('categories_all', now()->addMinutes(10), function () {
            return Category::all();
        });

        return response()->json($categories, 200);
    }

    public function store(CategoryStoreRequest $request)
    {
        $category = Category::create($request->only('name'));
        Cache::forget('categories_all');
        return response()->json($category, 201);
    }

    public function show($id)
    {
        $cacheKey = "category_{$id}";
        $category = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($id) {
            return Category::find($id);
        });

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
            Cache::forget('categories_all');
            Cache::forget("category_{$id}");
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
                Cache::forget('categories_all');
                Cache::forget("category_{$id}");
                return response()->json(null, 204);
            }
        } else {
            return response()->json(['message' => 'Category not found'], 404);
        }
    }
}
