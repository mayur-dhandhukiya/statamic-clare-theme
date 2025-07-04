<?php

namespace App\Tags;

use Statamic\Tags\Tags;
use Statamic\Entries\Entry;

class CartTag extends Tags
{
    protected static $handle = 'cart';

    public function index()
    {
        $cart = $this->getCart();
        if (!$cart) {
            return [];
        }

        return [
            'is_cart_items' => $this->isCartItems(),
            'cart_items' => $cart->get('cart_items'),
            'cart_total' => $cart->get('cart_total'),
        ];
    }

    public function isCartItems()
    {
        return count($this->cartItems());
    }

    public function cartItems()
    {
        $cart = $this->getCart();
        if (!$cart) {
            return [];
        }

        // return $cart->get('cart_items') ?? [];
        $items = $cart->get('cart_items') ?? [];

        return collect($items)->map(function ($item) {
            $product = Entry::find($item['product']);

            return [
                'product' => $product,
                'product_title' => $item['product_title'],
                'qty' => $item['qty'],
                'price' => $item['price'],
                'total' => $item['total'],
            ];
        })->toArray();

    }

    public function cartTotal()
    {
        $cart = $this->getCart();
        return $cart ? $cart->get('cart_total') : 0;
    }

    private function getCart()
    {
        if (session('customer_logged_in')) {
            $customerId = session('customer_id');
            $cart = Entry::query()
                ->where('collection', 'carts')
                ->where('customer', $customerId)
                ->first();
            session()->put('cart_id', $cart?->id() ?? null);
            return $cart;
        } elseif (session('cart_id')) {
            return Entry::find(session('cart_id'));
        }

        return null;
    }
}
