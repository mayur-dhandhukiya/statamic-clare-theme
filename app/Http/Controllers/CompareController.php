<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Statamic\Facades\Entry;

class CompareController extends Controller
{
    public function addToCompare(Request $request)
    {
        if (!session('customer_logged_in')) {
            return response()->json(['message' => 'Please log in to use compare'], 401);
        }

        $productId = $request->input('product_id');
        $customerId = session('customer_id');

        $product = Entry::find($productId);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $compare = Entry::query()
            ->where('collection', 'compares')
            ->where('customer', $customerId)
            ->first();

        if (!$compare) {
            $compare = Entry::make()
                ->collection('compares')
                ->slug(Str::random(10))
                ->set('customer', $customerId)
                ->set('compare_items', []);
        }

        $items = $compare->get('compare_items') ?? [];

        // Check if already in compare
        foreach ($items as $item) {
            if ($item['product'] == $productId) {
                return response()->json(['message' => 'Already in compare list']);
            }
        }

        // Limit compare items to 2
        if (count($items) >= 2) {
            return response()->json(['message' => 'Only 2 items can be compared at once'], 409);
        }

        $items[] = [
            'product' => $productId,
            'product_title' => $product->get('title'),
            'added_at' => now(),
        ];

        $compare->set('compare_items', $items);
        $compare->save();

        return response()->json(['message' => 'Added to compare list']);
    }

    public function removeFromCompare(Request $request)
    {
        if (!session('customer_logged_in')) {
            return response()->json(['message' => 'Login required'], 401);
        }

        $productId = $request->input('product_id');
        $customerId = session('customer_id');

        $compare = Entry::query()
            ->where('collection', 'compares')
            ->where('customer', $customerId)
            ->first();

        if (!$compare) return response()->json(['message' => 'Compare list not found'], 404);

        $items = array_filter($compare->get('compare_items') ?? [], fn($item) => $item['product'] != $productId);

        $compare->set('compare_items', array_values($items));
        $compare->save();

        return response()->json(['message' => 'Removed from compare list']);
    }

    public function clearCompare()
    {
        if (!session('customer_logged_in')) {
            return response()->json(['message' => 'Login required'], 401);
        }

        $compare = Entry::query()
            ->where('collection', 'compares')
            ->where('customer', session('customer_id'))
            ->first();

        if ($compare) {
            $compare->set('compare_items', []);
            $compare->save();
        }

        return response()->json(['message' => 'Compare list cleared']);
    }

    public function addToCartFromCompare(Request $request)
    {
        if (!session('customer_logged_in')) {
            return response()->json(['message' => 'Please log in to add items to cart'], 401);
        }

        $productId = $request->input('product_id');
        $customerId = session('customer_id');

        $product = Entry::find($productId);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $qty = 1;
        $price = $product->get('price');
        $title = $product->get('title');
        $total = $qty * $price;

        // Get or create cart
        $cart = Entry::query()
            ->where('collection', 'carts')
            ->where('customer', $customerId)
            ->first();

        if (!$cart) {
            $cart = Entry::make()
                ->collection('carts')
                ->slug(Str::random(10))
                ->set('customer', $customerId)
                ->set('cart_items', []);
        }

        $cartItems = $cart->get('cart_items') ?? [];

        $alreadyExists = false;
        foreach ($cartItems as &$item) {
            if ($item['product'] == $productId) {
                $item['qty'] += 1;
                $item['total'] = $item['qty'] * $item['price'];
                $alreadyExists = true;
                break;
            }
        }

        if (!$alreadyExists) {
            $cartItems[] = [
                'product' => $productId,
                'product_title' => $title,
                'qty' => $qty,
                'price' => $price,
                'total' => $total,
            ];
        }

        $cartTotal = array_sum(array_column($cartItems, 'total'));
        $cart->set('cart_items', $cartItems);
        $cart->set('cart_total', $cartTotal);
        $cart->save();

        session()->put('cart_id', $cart->id());

        // Remove from compare
        $compare = Entry::query()
            ->where('collection', 'compares')
            ->where('customer', $customerId)
            ->first();

        if ($compare) {
            $items = array_filter($compare->get('compare_items') ?? [], fn($item) => $item['product'] != $productId);
            $compare->set('compare_items', array_values($items));
            $compare->save();
        }

        return response()->json(['message' => 'Product moved from compare to cart']);
    }

    public function addAllToCartFromCompare()
    {
        if (!session('customer_logged_in')) {
            return response()->json(['message' => 'Please log in to add items to cart'], 401);
        }

        $customerId = session('customer_id');

        $compare = Entry::query()
            ->where('collection', 'compares')
            ->where('customer', $customerId)
            ->first();

        if (!$compare || empty($compare->get('compare_items'))) {
            return response()->json(['message' => 'No items in compare list'], 404);
        }

        $compareItems = $compare->get('compare_items');

        $cart = Entry::query()
            ->where('collection', 'carts')
            ->where('customer', $customerId)
            ->first();

        if (!$cart) {
            $cart = Entry::make()
                ->collection('carts')
                ->slug(Str::random(10))
                ->set('customer', $customerId)
                ->set('cart_items', []);
        }

        $cartItems = $cart->get('cart_items') ?? [];
        $added = 0;

        foreach ($compareItems as $item) {
            $productId = $item['product'];

            $product = Entry::find($productId);
            if (!$product) continue;

            $price = $product->get('price');
            $title = $product->get('title');

            $exists = false;

            foreach ($cartItems as &$cartItem) {
                if ($cartItem['product'] === $productId) {
                    $cartItem['qty'] += 1;
                    $cartItem['total'] = $cartItem['qty'] * $cartItem['price'];
                    $exists = true;
                    break;
                }
            }

            if (!$exists) {
                $cartItems[] = [
                    'product' => $productId,
                    'product_title' => $title,
                    'qty' => 1,
                    'price' => $price,
                    'total' => $price,
                ];
            }

            $added++;
        }

        $cartTotal = array_sum(array_column($cartItems, 'total'));
        $cart->set('cart_items', $cartItems);
        $cart->set('cart_total', $cartTotal);
        $cart->save();

        session()->put('cart_id', $cart->id());

        // Clear compare
        if ($added > 0) {
            $compare->set('compare_items', []);
            $compare->save();
        }

        return response()->json([
            'message' => $added > 0
                ? "$added item(s) moved from compare to cart"
                : "All compare items already exist in cart",
        ]);
}

}
