<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Statamic\Facades\Entry;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Statamic\Globals\GlobalSet;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function handleStripeSuccess(Request $request)
    {
        $orderSlug = $request->query('order');
        $sessionId = $request->query('session_id');

        if (!$orderSlug || !$sessionId) {
            abort(400, 'Missing payment info');
        }

        $order = Entry::query()
            ->where('collection', 'orders')
            ->where('slug', $orderSlug)
            ->first();

        if (!$order) {
            abort(404, 'Order not found');
        }

        // Get Stripe keys
        $globals = GlobalSet::findByHandle('payment_setting')->inDefaultSite();
        $stripeKey = $globals->get('secret_key');

        Stripe::setApiKey($stripeKey);

        // Get Stripe Session details
        $session = StripeSession::retrieve($sessionId);

        if (!$session || $session->payment_status !== 'paid') {
            abort(400, 'Payment not confirmed');
        }

        // Save transaction
        Entry::make()
            ->collection('transactions')
            ->slug(Str::random(10))
            ->set('title', 'Stripe Transaction')
            ->set('order', $order->id())
            ->set('customer', $order->get('customer'))
            ->set('payment_method', 'stripe')
            ->set('transaction_id', $session->payment_intent)
            ->set('amount', $session->amount_total / 100)
            ->set('currency', $session->currency)
            ->set('transaction_status', 'success')
            ->set('meta', [
                'email' => $session->customer_email,
                'session_id' => $session->id,
                'url' => $request->fullUrl()
            ])
            ->save();

        // Mark order as paid
        $order->set('order_status', 'paid')->save();

        // Redirect to thank-you page
        return redirect('/thank-you?status=success');
    }

    public function handleRazorpaySuccess(Request $request)
    {
        $orderId = $request->input('order_id');
        $paymentId = $request->input('razorpay_payment_id');

        if (!$paymentId || !$orderId) {
            return response()->json(['message' => 'Invalid payment response'], 422);
        }

        $order = Entry::query()
            ->where('collection', 'orders')
            ->where('id', $orderId)
            ->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Save transaction
        $transaction = Entry::make()
            ->collection('transactions')
            ->slug(Str::random(10))
            ->set('title', 'Transaction for Order ' . $order->get('order_number'))
            ->set('order', $order->id())
            ->set('customer', $order->get('customer'))
            ->set('payment_method', 'razorpay')
            ->set('transaction_id', $paymentId)
            ->set('transaction_status', 'success')
            ->set('amount', $order->get('order_total'))
            ->set('currency', 'INR')
            ->set('meta', ['type' => 'razorpay', 'response_time' => now()->toDateTimeString()])
            ->set('created_at', now()->format('Y-m-d H:i:s'));

        $transaction->save();

        // Update order status if needed
        $order->set('order_status', 'paid')->save();

        return response()->json(['message' => 'Payment and transaction saved']);
    }


}
