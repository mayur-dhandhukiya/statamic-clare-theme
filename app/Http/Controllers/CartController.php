<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Statamic\Facades\Entry;
use Statamic\Facades\Collection;
use Statamic\Facades\Stache;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $productId = $request->input('product_id');
        $qty = $request->input('quantity', 1);
        $sessionCartId = session()->get('cart_id');

        $product = Entry::find($productId);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $price = $product->get('price');
        $title = $product->get('title');
        $total = $qty * $price;

        $cart = null;

        if (session()->has('customer_logged_in')) {
            $customerId = session('customer_id');

            if ($sessionCartId && $existing = Entry::find($sessionCartId)) {
                $existing->set('customer', $customerId);
                $cart = $existing;
            } else {
                $cart = Entry::make()
                    ->collection('carts')
                    ->slug(Str::random(10))
                    ->set('customer', $customerId)
                    ->set('cart_items', []);
            }
        } else {
            if ($sessionCartId && $existing = Entry::find($sessionCartId)) {
                $cart = $existing;
            } else {
                $cart = Entry::make()
                    ->collection('carts')
                    ->slug(Str::random(10))
                    ->set('cart_items', []);
            }
        }

        $items = $cart->get('cart_items') ?? [];

        $found = false;
        foreach ($items as &$item) {
            if ($item['product'] == $productId) {
                $item['qty'] += $qty;
                $item['total'] = $item['qty'] * $item['price'];
                $found = true;
                break;
            }
        }

        if (!$found) {
            $items[] = [
                'product' => $productId,
                'product_title' => $title,
                'qty' => $qty,
                'price' => $price,
                'total' => $total,
            ];
        }

        $cartTotal = array_sum(array_column($items, 'total'));

        $cart->set('cart_items', $items);
        $cart->set('cart_total', $cartTotal);
        $cart->save();

        session()->put('cart_id', $cart->id());

        return response()->json([
            'message' => $found ? 'Item quantity updated in cart' : 'Item added to cart',
            'cart_total' => $cartTotal,
            'item_count' => count($items),
        ]);
    }

    public function updateItem(Request $request)
    {
        $productId = $request->input('product_id');
        $qty = (int) $request->input('quantity');

        $cartId = session('cart_id');
        if (!$cartId) {
            return response()->json(['message' => 'No cart found'], 404);
        }

        $cart = Entry::find($cartId);
        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        $items = $cart->get('cart_items') ?? [];

        foreach ($items as &$item) {
            if ($item['product'] === $productId) {
                $item['qty'] = $qty;
                $item['total'] = $item['price'] * $qty;
            }
        }

        $cartTotal = array_sum(array_column($items, 'total'));
        $cart->set('cart_items', $items);
        $cart->set('cart_total', $cartTotal);
        $cart->save();

        return response()->json(['message' => 'Cart updated successfully']);
    }

    public function removeItem(Request $request)
    {
        $productId = $request->input('product_id');

        $cartId = session('cart_id');
        if (!$cartId) {
            return response()->json(['message' => 'No cart found'], 404);
        }

        $cart = Entry::find($cartId);
        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        $items = $cart->get('cart_items') ?? [];

        $items = array_filter($items, function ($item) use ($productId) {
            return $item['product'] !== $productId;
        });

        $cartTotal = array_sum(array_column($items, 'total'));

        $cart->set('cart_items', array_values($items));
        $cart->set('cart_total', $cartTotal);
        $cart->save();

        return response()->json(['message' => 'Item removed from cart']);
    }


}
