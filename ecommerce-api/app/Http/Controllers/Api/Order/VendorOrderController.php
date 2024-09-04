<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderManagement\UpdateOrderItemRequest;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class VendorOrderController extends Controller
{
    public function index()
    {
        $vendor = auth()->user();
        $orders = Order::whereHas('orderItems.product', function ($query) use ($vendor) {
            $query->where('user_id', $vendor->id);
        })->with('orderItems.product')->get();

        return response()->json($orders, 200);
    }
    public function updateStatus(Request $request, $orderId)
    {
        $vendor = auth()->user();
        $order = Order::whereHas('orderItems.product', function ($query) use ($vendor) {
            $query->where('user_id', $vendor->id);
        })->findOrFail($orderId);

        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,canceled',
        ]);

        $order->update(['status' => $request->input('status')]);

        return response()->json(['message' => 'Order status updated successfully.'], 200);
    }
    public function updateOrderItemStatus(UpdateOrderItemRequest $request, $orderId)
    {
        $vendorId = auth()->id();

        $items = $request->validated('items');
        $updatedItems = [];
        $skippedItems = [];

        foreach ($items as $item) {
            $orderItem = OrderItem::where('id', $item['order_item_id'])
                ->where('order_id', $orderId)
                ->first();
            if ($orderItem && $orderItem->product->user_id == $vendorId) {
                $orderItem->status = $item['status'];
                $orderItem->save();
                $updatedItems[] = $orderItem->id;
            } else {
                $skippedItems[] = $item['order_item_id'];
            }
        }

        $message = count($updatedItems) > 0
            ? 'Order items updated successfully: ' . implode(', ', $updatedItems)
            : 'No items were updated.';

        if (count($skippedItems) > 0) {
            $message .= ' ...Skipped items: ' . implode(', ', $skippedItems);
        }

        return response()->json(['message' => $message], 200);
    }
}
