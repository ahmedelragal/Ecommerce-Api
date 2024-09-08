<?php

namespace App\Http\Controllers\Api\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentManagement\PaymentRequest;
use App\Mail\PaymentReceiptMail;
use App\Models\Order;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Stripe\PaymentIntent;
use Stripe\Webhook;
use Stripe\Stripe;
use App\Notifications\PaymentReceipt;

class PaymentController extends Controller
{

    public function createPaymentIntent($id)
    {
        $order = Order::find($id);
        if ($order) {
            if (auth()->id() == $order->user_id) {
                if ($order->payment_status != 'pending') {
                    return response()->json(['error' => 'Payment already made'], 409);
                }
                Stripe::setApiKey(Config::get('services.stripe.secret'));
                try {
                    $paymentIntent = PaymentIntent::create([
                        'amount' => (int) $order->total_price * 100, // convert to cents
                        'currency' => 'usd',
                        'metadata' => [
                            'customer_id' => auth()->id(),
                            'order_id' => $order->id
                        ],
                        'payment_method' => 'pm_card_visa',
                        'automatic_payment_methods' => [
                            'enabled' => true,
                            'allow_redirects' => 'never',
                        ],
                    ]);
                    return response()->json(['payment_id' => $paymentIntent->id]);
                } catch (Exception $e) {
                    return response()->json(['error' => $e->getMessage()], 500);
                }
            } else {
                return response()->json(['error' => 'Unauthorized to perform this action'], 401);
            }
        } else {
            return response()->json(['error' => 'Order not found'], 404);
        }
    }
    public function confirmPayment($orderId, $paymentIntentId)
    {
        $order = Order::find($orderId);
        if ($order) {
            if (auth()->id() == $order->user_id) {
                Stripe::setApiKey(config('services.stripe.secret'));

                try {
                    $paymentIntent = PaymentIntent::retrieve($paymentIntentId);
                    $paymentIntent->confirm();

                    if ($paymentIntent->status == 'succeeded') {
                        $order->payment_status = 'paid';
                        $order->save();

                        $order->user->notify(new PaymentReceipt($order, $paymentIntent));
                        return response()->json([
                            'message' => 'Payment succeeded',
                            'payment_intent' => $paymentIntent,
                        ], 200);
                    } else {
                        return response()->json([
                            'message' => 'Payment requires action',
                            'status' => $paymentIntent->status,
                        ], 202);
                    }
                } catch (Exception $e) {
                    return response()->json(['error' => $e->getMessage()], 500);
                }
            } else {
                return response()->json(['error' => 'Unauthorized to perform this action'], 401);
            }
        }
    }
}
