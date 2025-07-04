<?php

namespace App\Tags;

use Statamic\Tags\Tags;
use Statamic\Facades\Entry;

class CompareTag extends Tags
{
    protected static $handle = 'compare';

    public function index()
    {
        $compare = $this->getCustomerCompare();
        if (!$compare) {
            return [];
        }

        return [
            'is_compare_items' => $this->isCompareItems(),
            'compare_items' => $compare->get('compare_items') ?? [],
        ];
    }

    public function isCompareItems()
    {
        return count($this->compareItems());
    }

    /**
     * {{ compare:compare_items }}
     */
    public function compareItems()
    {
        // Only proceed if the customer is logged in
        if (!session('customer_logged_in')) {
            return [];
        }

        $compare = $this->getCustomerCompare();

        if (!$compare) {
            return [];
        }

        $items = $compare->get('compare_items') ?? [];

        return collect($items)->map(function ($item) {
            $product = Entry::find($item['product']);

            return [
                'product'        => $product,
                'product_title'  => $item['product_title'] ?? $product?->get('title'),
                'added_at'       => $item['added_at'] ?? null,
                'url'            => $product?->url(),
                'product_data'   => $product?->data(),
            ];
        })->toArray();
    }

    /**
     * Get compare entry for the logged-in customer
     */
    private function getCustomerCompare()
    {
        return Entry::query()
            ->where('collection', 'compares')
            ->where('customer', session('customer_id'))
            ->first();
    }
}
