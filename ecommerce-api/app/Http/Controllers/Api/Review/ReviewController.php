<?php

namespace App\Http\Controllers\Api\Review;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reviews\SubmitReviewRequest;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ReviewController extends Controller
{
    public function submitReview(SubmitReviewRequest $request, $productId)
    {
        $product = Product::find($productId);
        if ($product) {
            $existingReview = Review::where('user_id', auth()->id())
                ->where('product_id', $productId)
                ->first();
            if ($existingReview) {
                return response()->json(['error' => 'You have already reviewed this product'], 409);
            }

            $reviewContent = $request->input('review');
            if (strlen($reviewContent) < 20) {
                return response()->json(['error' => 'Your review is too short. Please provide more details.'], 403);
            }

            $blacklistedWords = ['spam', 'fake', 'scam', 'shit'];
            foreach ($blacklistedWords as $word) {
                if (stripos($reviewContent, $word) !== false) {
                    return response()->json(['error' => 'Your review contains prohibited words.'], 403);
                }
            }

            $linksCount = substr_count($reviewContent, 'http');
            if ($linksCount > 2) {
                return response()->json(['error' => 'Your review contains too many links.'], 403);
            }

            $review = Review::create([
                'user_id' => auth()->id(),
                'product_id' => $productId,
                'rating' => $request->rating,
                'review' => $request->review,
                'approved' => false,
            ]);

            return response()->json(['message' => 'Review submitted successfully and is awaiting approval by admin', 'review' => $review], 201);
        } else {
            return response()->json(['error' => 'Product not found'], 404);
        }
    }
    public function approveReview($reviewId)
    {
        $review = Review::find($reviewId);
        if ($review) {
            if ($review->approved) {
                return response()->json(['error' => 'Review already approved'], 409);
            } else {
                $review->approved = true;
                $review->save();
                return response()->json(['message' => 'Review approved successfully', 'review' => $review], 200);
            }
        } else {
            return response()->json(['error' => 'Review not found'], 404);
        }
    }
    public function getProductReviews($productId)
    {
        $reviews = Cache::remember("productReviews_$productId", now()->addMinutes(10), function () use ($productId) {
            $product = Product::find($productId);

            if (!$product) {
                return null;
            }

            return $product->reviews()->where('approved', true)->get();
        });

        if (!$reviews) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        if ($reviews->isEmpty()) {
            return response()->json(['message' => 'No reviews found'], 404);
        }

        return response()->json($reviews);
    }
}
