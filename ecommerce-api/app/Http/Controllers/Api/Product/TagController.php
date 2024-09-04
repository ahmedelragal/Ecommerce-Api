<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductManagement\TagStoreRequst;
use App\Http\Requests\ProductManagement\TagUpdateRequest;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index()
    {
        return Tag::all();
    }

    public function store(TagStoreRequst $request)
    {
        $tag = Tag::create($request->only('name'));
        return response()->json($tag, 201);
    }

    public function show($id)
    {
        $tag = Tag::find($id);
        if ($tag) {
            return response()->json($tag, 200);
        } else {
            return response()->json(['message' => 'Tag not found'], 404);
        }
    }

    public function update(TagUpdateRequest $request, $id)
    {
        $tag = Tag::find($id);
        if ($tag) {
            $tag->update($request->only('name'));
            return response()->json($tag, 200);
        } else {
            return response()->json(['message' => 'Tag not found'], 404);
        }
    }
    public function destroy($id)
    {
        $tag = Tag::find($id);
        if ($tag) {
            $tag->delete();
            return response()->json(null, 204);
        } else {
            return response()->json(['message' => 'Tag not found'], 404);
        }
    }
}
