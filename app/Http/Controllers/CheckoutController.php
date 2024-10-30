<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Restaurant;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\StripeClient;

class CheckoutController extends Controller
{
    function checkout($code, $type)
    {
        $restaurant = Restaurant::where('code', $code)->first();
        $settings = [];
        foreach ($restaurant->settings as $setting) {
            $settings[$setting->key] = $setting->value;
        }

        $branches = [];
        if ($type == 'delivery') {
            $branches = $restaurant->branches;
        }

        return Inertia::render('Checkout', compact('restaurant', 'settings', 'type', 'branches'));
    }

    function make_order(Request $request, $code, $type)
    {
        $restaurant = Restaurant::where('code', $code)->first();
        $settings = [];
        foreach ($restaurant->settings as $setting) {
            $settings[$setting->key] = $setting->value;
        }

        $order = new Order();
        $order->user_id = Auth::id();
        $order->restaurant_id = $restaurant->id;
        $order->order_type = $type;
        $order->branch_id = Branch::find($request->form['branch_id'])->id ?? null;

        $total = 0;
        foreach ($request->cart as $item) {
            $total += $item['price'];
        }
        $order->total = $total;

        if (!Auth::check()) {
            $order->guest_name = $request->form['name'];
            $order->guest_email = $request->form['email'];
            $order->guest_phone = $request->form['phone'];
        }

        if ($type == 'delivery') {
            $order->guest_address = $request->form['address'];
        }

        if ($type == 'in-restaurant') {
            $order->table_number = $request->form['table_number'];
        }
        $order->save();

        foreach ($request->cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'menu_item_id' => $item['menu_item']['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ]);
        }

        if ($request->form['payment_method'] == 'cash') {
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
                        'currency' => $settings['currency'],
                        'unit_amount' => (($item->price / $item->quantity) - (($item->price / $item->quantity) * $settings['membership_discount'])) * 100,
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
                    'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}&order_id=' . $order->id,
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
