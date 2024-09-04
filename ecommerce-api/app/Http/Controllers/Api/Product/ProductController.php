<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductManagement\ProductStoreRequest;
use App\Http\Requests\ProductManagement\ProductUpdateRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
    public function index()
    {
        return Product::with('categories', 'tags', 'images')->get();
    }
    public function store(ProductStoreRequest $request)
    {
        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'user_id' => Auth::id(),
        ]);
        if ($request->has('category_ids')) {
            $product->categories()->attach($request->category_ids);
        }

        if ($request->has('tag_ids')) {
            $product->tags()->attach($request->tag_ids);
        }
        $product->load('categories', 'tags', 'images');
        return response()->json($product, 201);
    }
    public function show($id)
    {
        $product = Product::with('categories', 'tags', 'images')->find($id);
        if ($product) {
            return response()->json($product, 200);
        } else {
            return response()->json(['message' => 'Product not found'], 404);
        }
    }

    public function update(ProductUpdateRequest $request, $id)
    {
        $product = Product::find($id);
        if ($product) {
            if (Auth::id() != $product->user_id) {
                if (!auth()->user()->can('admin privelages')) {
                    return response()->json(['message' => 'Unauthorized to edit product'], 403);
                }
            }
            $product->update($request->only('name', 'description', 'price'));
            if ($request->has('category_ids')) {
                $product->categories()->sync($request->category_ids);
            }
            if ($request->has('tag_ids')) {
                $product->tags()->sync($request->tag_ids);
            }
            $product->load('categories', 'tags', 'images');
            return response()->json($product, 200);
        } else {
            return response()->json(['message' => 'Product not found'], 404);
        }
    }
    public function destroy($id)
    {
        $product = Product::find($id);
        if ($product) {
            if (Auth::id() != $product->user_id) {
                if (!auth()->user()->can('admin privelages')) {
                    return response()->json(['message' => 'Unauthorized to edit product'], 403);
                }
            }
            $product->delete();
        } else {
            return response()->json(['message' => 'Product not found'], 404);
        }
    }
}
