<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Statamic\Facades\Entry;
use Illuminate\Support\Str;
use Carbon\Carbon;

class WishlistController extends Controller
{
    public function addToWishlist(Request $request)
    {
        if (!session('customer_logged_in')) {
            return response()->json(['err_type' => 'not-login', 'message' => 'Please log in to use wishlist'], 401);
        }

        $productId = $request->input('product_id');
        $customerId = session('customer_id');

        $product = Entry::find($productId);
        if (!$product) {
            return response()->json(['err_type' => '', 'message' => 'Product not found'], 404);
        }

        $title = $product->get('title');

        // Find or create wishlist for logged-in customer
        $wishlist = Entry::query()
            ->where('collection', 'wishlists')
            ->where('customer', $customerId)
            ->first();

        if (!$wishlist) {
            $wishlist = Entry::make()
                ->collection('wishlists')
                ->slug(Str::random(10))
                ->set('customer', $customerId)
                ->set('wishlist_items', []);
        }

        $items = $wishlist->get('wishlist_items') ?? [];

        foreach ($items as $item) {
            if ($item['product'] === $productId) {
                return response()->json(['err_type' => '', 'message' => 'Already in wishlist']);
            }
        }

        $items[] = [
            'product' => $productId,
            'product_title' => $title,
            'added_at' => now(),
        ];

        $wishlist->set('wishlist_items', $items);
        $wishlist->save();

        return response()->json(['err_type' => '', 'message' => 'Added to wishlist']);
    }

    public function removeItem(Request $request)
    {
        if (!session('customer_logged_in')) {
            return response()->json(['message' => 'Please log in to modify wishlist'], 401);
        }

        $productId = $request->input('product_id');
        $customerId = session('customer_id');

        $product = Entry::find($productId);
        if (!$product) {
            return response()->json(['message' => 'Product or Product ID not found'], 404);
        }

        $wishlist = Entry::query()
            ->where('collection', 'wishlists')
            ->where('customer', $customerId)
            ->first();

        if (!$wishlist) {
            return response()->json(['message' => 'Wishlist not found'], 404);
        }

        $items = array_filter($wishlist->get('wishlist_items') ?? [], function ($item) use ($productId) {
            return $item['product'] != $productId;
        });

        $wishlist->set('wishlist_items', array_values($items));
        $wishlist->save();

        return response()->json(['message' => 'Item removed from wishlist']);
    }

    /**
     * Clear all items from the logged-in user's wishlist
     */
    public function clearWishlist()
    {
        if (!session('customer_logged_in')) {
            return response()->json(['message' => 'Please log in to clear wishlist'], 401);
        }

        $customerId = session('customer_id');

        $wishlist = Entry::query()
            ->where('collection', 'wishlists')
            ->where('customer', $customerId)
            ->first();

        if (!$wishlist) {
            return response()->json(['message' => 'Wishlist not found'], 404);
        }

        $wishlist->set('wishlist_items', []);
        $wishlist->save();

        return response()->json(['message' => 'Wishlist cleared successfully']);
    }

    public function addToCartFromWishlist(Request $request)
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

        $found = false;
        foreach ($cartItems as &$item) {
            if ($item['product'] == $productId) {
                $item['qty'] += $qty;
                $item['total'] = $item['qty'] * $price;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $cartItems[] = [
                'product' => $productId,
                'product_title' => $title,
                'qty' => $qty,
                'price' => $price,
                'total' => $qty * $price,
            ];
        }

        $cartTotal = array_sum(array_column($cartItems, 'total'));

        $cart->set('cart_total', $cartTotal);
        $cart->set('cart_items', $cartItems);
        $cart->save();

        session()->put('cart_id', $cart->id());

        // Remove from wishlist
        $wishlist = Entry::query()
            ->where('collection', 'wishlists')
            ->where('customer', $customerId)
            ->first();

        if ($wishlist) {
            $wishlistItems = $wishlist->get('wishlist_items') ?? [];
            $wishlistItems = array_filter($wishlistItems, fn($item) => $item['product'] != $productId);
            $wishlist->set('wishlist_items', array_values($wishlistItems));
            $wishlist->save();
        }

        return response()->json(['message' => 'Product added to cart from wishlist']);
    }

    public function addAllToCartFromWishlist(Request $request)
    {
        if (!session('customer_logged_in')) {
            return response()->json(['message' => 'Please log in to add items to cart'], 401);
        }

        $customerId = session('customer_id');

        $wishlist = Entry::query()
            ->where('collection', 'wishlists')
            ->where('customer', $customerId)
            ->first();

        if (!$wishlist || empty($wishlist->get('wishlist_items'))) {
            return response()->json(['message' => 'No wishlist items found'], 404);
        }

        $wishlistItems = $wishlist->get('wishlist_items');
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

        foreach ($wishlistItems as $wishlistItem) {
            $productId = $wishlistItem['product'];
            $product = Entry::find($productId);
            if (!$product) continue;

            $qty = 1;
            $price = $product->get('price');
            $title = $product->get('title');
            $found = false;

            foreach ($cartItems as &$item) {
                if ($item['product'] == $productId) {
                    $item['qty'] += $qty;
                    $item['total'] = $item['qty'] * $price;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $cartItems[] = [
                    'product' => $productId,
                    'product_title' => $title,
                    'qty' => $qty,
                    'price' => $price,
                    'total' => $qty * $price,
                ];
            }

            $added++;
        }

        $cartTotal = array_sum(array_column($cartItems, 'total'));

        $cart->set('cart_total', $cartTotal);
        $cart->set('cart_items', $cartItems);
        $cart->save();

        session()->put('cart_id', $cart->id());

        // Clear wishlist
        $wishlist->set('wishlist_items', []);
        $wishlist->save();

        return response()->json([
            'message' => "$added item(s) transferred from wishlist to cart",
        ]);
    }

}
