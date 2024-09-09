<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductManagement\TagStoreRequst;
use App\Http\Requests\ProductManagement\TagUpdateRequest;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TagController extends Controller
{
    public function index()
    {
        $tags = Cache::remember('tags_all', now()->addMinutes(10), function () {
            return Tag::all();
        });

        return response()->json($tags, 200);
    }

    public function store(TagStoreRequst $request)
    {
        $tag = Tag::create($request->only('name'));
        Cache::forget('tags_all');
        return response()->json($tag, 201);
    }

    public function show($id)
    {
        $cacheKey = "tag_{$id}";
        $tag = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($id) {
            return Tag::find($id);
        });
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
            Cache::forget('tags_all');
            Cache::forget("tag_{$id}");

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
            Cache::forget('tags_all');
            Cache::forget("tag_{$id}");
            return response()->json(null, 204);
        } else {
            return response()->json(['message' => 'Tag not found'], 404);
        }
    }
}
