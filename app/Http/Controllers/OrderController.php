<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Statamic\Entries\Entry;
use Barryvdh\DomPDF\Facade\Pdf;
use Statamic\Facades\Antlers;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Statamic\Globals\GlobalSet;

class OrderController extends Controller
{
    public function placeOrder(Request $request)
    {
        if (!session('customer_logged_in')) {
            return response()->json(['err_type' => 'not-login', 'message' => 'Please log in to place an order'], 401);
        }

        $customerId = session('customer_id');

        // Validation rules
        $rules = [
            'billing_first_name' => 'required|string|max:100',
            'billing_last_name' => 'required|string|max:100',
            'billing_email' => 'required|email',
            'billing_phone' => 'required|string|max:20',
            'billing_address' => 'required|string|max:255',
            'billing_city' => 'required|string|max:100',
            'billing_state' => 'required|string|max:100',
            'billing_zip' => 'required|string|max:20',
            'billing_country' => 'required|string|max:100',
            'payment_method' => 'required|in:cod,stripe,razorpay',
            'agree_terms' => 'accepted',
        ];

        if ($request->has('shipping_enabled')) {
            $rules = array_merge($rules, [
                'shipping_first_name' => 'required|string|max:100',
                'shipping_last_name' => 'required|string|max:100',
                'shipping_email' => 'required|email',
                'shipping_phone' => 'required|string|max:20',
                'shipping_address' => 'required|string|max:255',
                'shipping_city' => 'required|string|max:100',
                'shipping_state' => 'required|string|max:100',
                'shipping_zip' => 'required|string|max:20',
                'shipping_country' => 'required|string|max:100',
            ]);
        }

        // Validate request
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['err_type' => '', 'errors' => $validator->errors()], 422);
        }

        // Get Cart
        $cart = Entry::query()
            ->where('collection', 'carts')
            ->where('customer', $customerId)
            ->first();

        if (!$cart || empty($cart->get('cart_items'))) {
            return response()->json(['err_type' => '', 'message' => 'Cart is empty'], 400);
        }

        // Prepare Order Items
        $orderItems = collect($cart->get('cart_items'))->map(function ($item) {
            return [
                'type' => 'product_item',
                'product' => $item['product'],
                'product_title' => $item['product_title'],
                'qty' => $item['qty'],
                'price' => $item['price'],
                'total' => $item['total'],
                'added_at' => now()->format('Y-m-d H:i:s'),
                'enabled' => true,
            ];
        })->toArray();

        // Shipping Info Handling
        $shippingEnabled = $request->has('shipping_enabled');
        $shipping = $shippingEnabled ? [
            'shipping_first_name' => $request->input('shipping_first_name'),
            'shipping_last_name' => $request->input('shipping_last_name'),
            'shipping_email' => $request->input('shipping_email'),
            'shipping_phone' => $request->input('shipping_phone'),
            'shipping_address' => $request->input('shipping_address'),
            'shipping_city' => $request->input('shipping_city'),
            'shipping_state' => $request->input('shipping_state'),
            'shipping_zip' => $request->input('shipping_zip'),
            'shipping_country' => $request->input('shipping_country'),
        ] : [
            'shipping_first_name' => $request->input('billing_first_name'),
            'shipping_last_name' => $request->input('billing_last_name'),
            'shipping_email' => $request->input('billing_email'),
            'shipping_phone' => $request->input('billing_phone'),
            'shipping_address' => $request->input('billing_address'),
            'shipping_city' => $request->input('billing_city'),
            'shipping_state' => $request->input('billing_state'),
            'shipping_zip' => $request->input('billing_zip'),
            'shipping_country' => $request->input('billing_country'),
        ];

        $orderNumber = $this->generateOrderNumber();

        $paymentMethod = $request->input('payment_method') ?? 'cod';

        // Create Order Entry
        $order = Entry::make()
            ->collection('orders')
            ->slug(Str::random(10))
            ->set('title', 'Order ' . $orderNumber)
            ->set('order_number', $orderNumber)
            ->set('customer', $customerId)
            ->set('order_status', 'pending')
            ->set('order_total', $cart->get('cart_total'))
            ->set('payment_method', $paymentMethod)
            ->set('agree_terms', true)
            ->set('order_notes', $request->input('order_notes'))
            ->set('created_at', now()->format('Y-m-d H:i:s'))
            ->set('updated_at', now()->format('Y-m-d H:i:s'))
            // Billing Info
            ->set('billing_first_name', $request->input('billing_first_name'))
            ->set('billing_last_name', $request->input('billing_last_name'))
            ->set('billing_email', $request->input('billing_email'))
            ->set('billing_phone', $request->input('billing_phone'))
            ->set('billing_address', $request->input('billing_address'))
            ->set('billing_city', $request->input('billing_city'))
            ->set('billing_state', $request->input('billing_state'))
            ->set('billing_zip', $request->input('billing_zip'))
            ->set('billing_country', $request->input('billing_country'))
            // Shipping Info
            ->set('shipping_enabled', $shippingEnabled)
            ->merge($shipping)
            // Items
            ->set('order_items', $orderItems);

        $order->save();

        $this->createOrUpdateAddress($order, 'billing');

        if ($shippingEnabled) {
            $this->createOrUpdateAddress($order, 'shipping');
        }

        session(['last_order_id' => $order->id()]);

        // Empty Cart
        $cart->set('cart_items', []);
        $cart->set('cart_total', 0);
        $cart->save();

        switch ($paymentMethod) {
            case 'cod':
                // Direct success flow
                return response()->json([
                    'err_type' => '',
                    'message' => 'Order placed successfully. You selected Cash on Delivery.',
                    'redirect_url' => '/thank-you',
                ]);

            case 'stripe':
                return $this->initiateStripePayment($order);

            case 'razorpay':
                return $this->initiateRazorpayPayment($order);

            default:
                return response()->json(['message' => 'Invalid payment method selected'], 422);
        }

        return response()->json(['err_type' => '', 'message' => 'Order placed successfully']);
    }

    private function initiateStripePayment($order, $cancel_url = '/thank-you')
    {
        $globals = GlobalSet::findByHandle('payment_setting')->inDefaultSite(); // change 'payment_setting' to your actual handle
        $stripeKey = $globals->get('secret_key');
        $stripe_public_key = $globals->get('public_key');

        if (!$stripeKey) {
            abort(500, 'Stripe secret key is missing.');
        }

        Stripe::setApiKey($stripeKey);

        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'USD',
                    'product_data' => [
                        'name' => 'Order ' . $order->get('order_number'),
                    ],
                    'unit_amount' => intval($order->get('order_total') * 100),
                    // 'unit_amount' => intval(1 * 100),
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            // 'success_url' => url('/thank-you?order=' . $order->slug()),
            'success_url' => url('/stripe/payment-success?order=' . $order->slug() . '&session_id={CHECKOUT_SESSION_ID}'),
            // 'cancel_url' => url('/thank-you'),
            'cancel_url' => url($cancel_url),
        ]);

        // // Now update success_url using the session ID
        // StripeSession::update(
        //     $session->id,
        //     [
        //         'metadata' => [
        //             'success_url' => url('/stripe/payment-success?order=' . $order->slug() . '&session_id=' . $session->id)
        //         ]
        //     ]
        // );

        return response()->json([
            'message' => 'Redirecting to Stripe...',
            'stripe' => true,
            'session_id' => $session->id,
            'stripe_public_key' => $stripe_public_key,
        ]);
    }

    private function initiateRazorpayPayment($order)
    {
        $globals = GlobalSet::findByHandle('payment_setting')->inDefaultSite();
        $razorpayKey = $globals->get('razorpay_key');

        // Send order data for Razorpay frontend JS
        return response()->json([
            'message' => 'Processing Razorpay payment...',
            'razorpay' => true,
            'order_id' => $order->id(),
            'amount' => $order->get('order_total'),
            'currency' => 'USD',
            'name' => $order->get('billing_first_name') . ' ' . $order->get('billing_last_name'),
            'email' => $order->get('billing_email'),
            'contact' => $order->get('billing_phone'),
            'key' => $razorpayKey,
        ]);
    }

    public function payPendingOrder(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:stripe,razorpay',
            'order_id' => 'required|string',
        ]);

        $order = Entry::query()
            ->where('collection', 'orders')
            ->where('id', $request->order_id)
            ->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        if ($order->get('order_status') !== 'pending') {
            return response()->json(['message' => 'Only pending orders can be paid'], 422);
        }

         // ✅ Update the payment method in order
        $order->set('payment_method', $request->payment_method);
        $order->save();

        switch ($request->payment_method) {
            case 'stripe':
                return $this->initiateStripePayment($order, '/account-order');
            case 'razorpay':
                return $this->initiateRazorpayPayment($order);
            default:
                return response()->json(['message' => 'Invalid payment method'], 400);
        }
    }


    private function generateOrderNumber()
    {
        // Get the latest order with numeric order_number
        $lastOrder = Entry::query()
            ->where('collection', 'orders')
            ->where('order_number', '!=', null)
            ->orderByDesc('order_number')
            ->first();

        if ($lastOrder && is_numeric($lastOrder->get('order_number'))) {
            $nextNumber = (int) $lastOrder->get('order_number') + 1;
        } else {
            $nextNumber = 1001; // Start from 1001
        }

        return (string) $nextNumber;
    }

    private function createOrUpdateAddress(Entry $order, string $type)
    {
        $customerId = session('customer_id');

        $entry = Entry::query()
            ->where('collection', 'addresses')
            ->where('address_type', $type)
            ->whereJsonContains('customer', $customerId)
            ->first();

        if (! $entry) {
            $entry = Entry::make()
                ->collection('addresses')
                ->slug(Str::uuid());
        }

        $prefix = $type . '_';

        $entry
            ->set('customer', $customerId)
            ->set('address_type', $type)
            ->set('first_name', $order->get($prefix . 'first_name'))
            ->set('last_name', $order->get($prefix . 'last_name'))
            ->set('email', $order->get($prefix . 'email'))
            ->set('phone_number', $order->get($prefix . 'phone'))
            ->set('address', $order->get($prefix . 'address'))
            ->set('city', $order->get($prefix . 'city'))
            ->set('state', $order->get($prefix . 'state'))
            ->set('pin_code', $order->get($prefix . 'zip'))
            ->set('country', $order->get($prefix . 'country'))
            ->save();
    }

    public function thankYou(Request $request)
    {
        if (!session()->has('last_order_id')) {
            return redirect('/'); // fallback
        }

        $orderId = session('last_order_id');
        $order = Entry::find($orderId);

        if (!$order || $order->collection()->handle() !== 'orders') {
            return redirect('/');
        }

        // Extract items from order
        $orderItems = $order->get('order_items') ?? [];

        // Map product details
        $order_items = collect($orderItems)->map(function ($item) {
            $product = Entry::find($item['product']);

            return [
                'product' => $product, // whole entry
                'product_title' => $item['product_title'],
                'qty' => $item['qty'],
                'price' => $item['price'],
                'total' => $item['total'],
                'image' => $product?->get('thumb_image'),
                'slug' => $product?->slug(),
            ];
        })->toArray();

        return view('thank-you', ['order' => $order, 'order_items' => $order_items]);
    }

    public function downloadInvoice($orderId)
    {
        $order = Entry::find($orderId);

        if (! $order) {
            abort(404);
        }

        // $html = View::make('invoice', $order)->render();
        $html = View::make('invoice', ['order' => $order])->render();

        // Use DOMPDF manually (no package install)
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="invoice-' . $order->get('order_number') . '.pdf"',
        ]);
    }

}
