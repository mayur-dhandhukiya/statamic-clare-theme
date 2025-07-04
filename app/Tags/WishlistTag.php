<?php

namespace App\Tags;

use Statamic\Tags\Tags;
use Statamic\Facades\Entry;

class WishlistTag extends Tags
{
    protected static $handle = 'wishlist';

    public function index()
    {
        $wishlist = $this->getCustomerWishlist();
        if (!$wishlist) {
            return [];
        }

        return [
            'is_wishlist_items' => $this->isWishlistItems(),
            'wishlist_items' => $wishlist->get('wishlist_items') ?? [],
        ];
    }

    public function isWishlistItems()
    {
        return count($this->wishlistItems());
    }

    /**
     * {{ wishlist:wishlist_items }}
     */
    public function wishlistItems()
    {
        // Only proceed if the customer is logged in
        if (!session('customer_logged_in')) {
            return [];
        }

        $wishlist = $this->getCustomerWishlist();

        if (!$wishlist) {
            return [];
        }

        $items = $wishlist->get('wishlist_items') ?? [];

        return collect($items)->map(function ($item) {
            $product = Entry::find($item['product']);

            return [
                'product'        => $product,
                'product_title'  => $item['product_title'] ?? $product?->get('title'),
                'added_at'       => $item['added_at'] ?? null,
                'url'            => $product?->url(),
                'product_data'   => $product?->data(), // full entry data (optional)
            ];
        })->toArray();
    }

    /**
     * Get wishlist for the logged-in customer
     */
    private function getCustomerWishlist()
    {
        return Entry::query()
            ->where('collection', 'wishlists')
            ->where('customer', session('customer_id'))
            ->first();
    }
}