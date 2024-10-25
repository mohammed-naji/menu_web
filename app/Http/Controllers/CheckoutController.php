<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\StripeClient;

class CheckoutController extends Controller
{
    function checkout(Request $request)
    {
        return Inertia::render('Checkout');
    }

    function make_order(Request $request)
    {
        $setting = Setting::find(1);
        $order = Order::with('orderItems.menuItem')->find($request->order_id);
        if (!Auth::check()) {
            $order->visitor_name = $request->name;
            $order->visitor_email = $request->email;
            $order->visitor_phone = $request->phone;
        }

        if ($order->order_type == 'delivery') {
            $order->visitor_address = $request->address;
        }

        if ($order->order_type == 'in-restaurant') {
            $order->table_number = $request->table_number;
        }
        $order->save();

        $discount = 0;
        if (Auth::check()) {
            $discount = $setting->membership_discount / 100;
        }

        if ($request->payment_method == 'cash') {
            $order->status = 'in-process';
            $order->save();
            return response()->json(['success' => 1]);
        } else {
            // Set your Stripe secret key
            Stripe::setApiKey(env('STRIPE_SECRET'));

            $line_items = [];
            foreach ($order->orderItems as $item) {
                $line_items[] = [
                    'price_data' => [
                        'currency' => $setting->currency,
                        'unit_amount' => (($item->price / $item->quantity) - (($item->price / $item->quantity) * $discount)) * 100,
                        'product_data' => [
                            'name' => $item->menuItem->trans_name,
                        ],
                    ],
                    'quantity' => $item->quantity,
                ];
            }

            try {
                // Create the checkout session
                $checkoutSession = Session::create([
                    'payment_method_types' => ['card'],
                    'line_items' => $line_items,
                    'mode' => 'payment',
                    'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}&order_id=' . $request->order_id,
                    'cancel_url' => route('checkout.cancel'),
                ]);

                // Return the session ID to the frontend
                return response()->json(['id' => $checkoutSession->id]);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }
    }

    public function success(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $sessionId = $request->query('session_id');
        $orderId = $request->query('order_id');

        if ($sessionId) {
            try {
                // Retrieve the Checkout Session from Stripe using the session ID
                $stripe = new StripeClient(env('STRIPE_SECRET'));
                $session = $stripe->checkout->sessions->retrieve($sessionId, [
                    'expand' => ['line_items']
                ]);

                if ($session->payment_status == 'paid') {
                    // Update the order status to 'paid'
                    $order = Order::find($orderId);
                    $order->status = 'confirmed';
                    $order->save();
                }
                // Pass session data to the frontend (e.g., using Inertia)
                return Inertia::render('Front/CheckoutSuccess', [
                    'session' => $session
                ]);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Unable to retrieve session.'], 500);
            }
        }

        return response()->json(['error' => 'No session ID provided.'], 400);
    }

    public function cancel()
    {
        // Handle cancel redirect
        return Inertia::render('Front/CheckoutCancel'); // Return Inertia page on cancel
    }
}
