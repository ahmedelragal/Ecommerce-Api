<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ProductImageController extends Controller
{
    public function upload(Request $request, $productId)
    {
        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif',
        ]);

        $product = Product::find($productId);
        if ($product) {
            if (Auth::id() != $product->user_id) {
                return response()->json(['message' => 'Unauthorized to edit product'], 403);
            } elseif ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('product_images', 'public');

                    $product->images()->create([
                        'image_path' => $path,
                    ]);
                }
                return response()->json(['message' => 'Images uploaded successfully'], 201);
            } else {
                return response()->json(['message' => 'No images to upload'], 404);
            }
        } else {
            return response()->json(['message' => 'Product not found'], 404);
        }
    }

    public function delete($productId, $imageId)
    {
        $product = Product::find($productId);
        if ($product) {
            if (Auth::id() != $product->user_id) {
                return response()->json(['message' => 'Unauthorized to edit product'], 403);
            } else {
                $image = $product->images()->where('id', $imageId)->firstOrFail();
                Storage::disk('public')->delete($image->image_path);
                $image->delete();
                return response()->json(['message' => 'Image deleted successfully']);
            }
        } else {
            return response()->json(['message' => 'Product not found'], 404);
        }
    }

    public function getImages($productId)
    {
        $product = Product::with('images')->find($productId);
        if ($product) {
            return response()->json($product->images);
        } else {
            return response()->json(['message' => 'Product not found'], 404);
        }
    }
}
