<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderManagement\CreateOrderRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(CreateOrderRequest $request)
    {
        $user = auth()->user();
        $items = $request->input('items');
        $totalPrice = 0;

        $order = Order::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'total_price' => 0,
        ]);

        foreach ($items as $item) {
            $product = Product::find($item['product_id']);
            $price = $product->price * $item['quantity'];
            $totalPrice += $price;

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $price,
            ]);
        }

        $order->update(['total_price' => $totalPrice]);

        return response()->json($order->load('orderItems.product'), 201);
    }
    public function index()
    {
        $user = auth()->user();
        $orders = $user->orders()->with('orderItems.product')->get();
        return response()->json($orders, 200);
    }

    public function cancel($orderId)
    {
        $order = Order::where('user_id', auth()->id())->where('id', $orderId)->firstOrFail();

        if ($order->status === 'pending') {
            $order->update(['status' => 'canceled']);
            return response()->json(['message' => 'Order canceled successfully.'], 200);
        }

        return response()->json(['message' => 'Only pending orders can be canceled.'], 400);
    }
    public function track($orderId)
    {
        $order = Order::where('user_id', auth()->id())->with('orderItems.product')->findOrFail($orderId);

        return response()->json($order, 200);
    }
}
