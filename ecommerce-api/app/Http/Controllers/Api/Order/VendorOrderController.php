<?php

namespace App\Http\Controllers\Api\Order;

use App\Events\OrderStatusUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderManagement\UpdateOrderItemRequest;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;


class VendorOrderController extends Controller
{
    public function index()
    {
        $vendor = auth()->user();
        $orders = Order::whereHas('orderItems.product', function ($query) use ($vendor) {
            $query->where('user_id', $vendor->id);
        })->with('orderItems.product')->get();
        if ($orders) {
            return response()->json($orders, 200);
        } else {
            return response()->json(['message' => 'No Orders Found for this Vendor'], 404);
        }
    }
    public function updateStatus(Request $request, $orderId)
    {
        $vendor = auth()->user();
        $order = Order::whereHas('orderItems.product', function ($query) use ($vendor) {
            $query->where('user_id', $vendor->id);
        })->find($orderId);

        if ($order) {
            $request->validate([
                'status' => 'required|in:pending,processing,shipped,delivered,canceled',
            ]);

            $order->update(['status' => $request->input('status')]);
            $user_id = $order->user_id;
            Cache::forget("orderDetails_$orderId");
            Cache::forget("user_orders_$user_id");
            event(new OrderStatusUpdated($order));

            return response()->json(['message' => 'Order status updated successfully.'], 200);
        } else {
            return response()->json(['message' => 'Order Not Found or Does Not Belong to this Vendor'], 404);
        }
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
        if (count($updatedItems) > 0) {
            Cache::forget("orderDetails_$orderId");
            $order = Order::find($orderId);
            $user_id = $order->user_id;
            Cache::forget("user_orders_$user_id");
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
