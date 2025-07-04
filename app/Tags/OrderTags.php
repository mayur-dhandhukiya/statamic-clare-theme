<?php

namespace App\Tags;

use Statamic\Tags\Tags;
use Statamic\Entries\Entry;
use Statamic\Globals\GlobalSet;

class OrderTags extends Tags
{
    protected static $handle = 'order';

    /**
     * {{ order:check }}
     */
    public function check()
    {
        $order = $this->getLastOrder();

        return (!$order || is_null($order) || empty($order)) ? false : true;
    }

    /**
     * {{ order:details }}
     */
    public function details()
    {
        $order = $this->getLastOrder();

        if (!$order) {
            return [];
        }

        $items = $order->get('order_items') ?? [];

        $mappedItems = collect($items)->map(function ($item) {
            $product = Entry::find($item['product']);

            return [
                'product' 		=> $product,
                'product_title' => $item['product_title'] ?? $product?->get('title'),
                'qty'           => $item['qty'],
                'price'         => $item['price'],
                'total'         => $item['total'],
                'slug'          => $product?->slug(),
                'thumb_image'   => $product?->get('thumb_image'),
                'url'           => $product?->url(),
            ];
        })->toArray();

        $payment_method = $order->get('payment_method') ?? '-';

        $globals = GlobalSet::findByHandle('payment_setting')->inDefaultSite();
        $paymentMethods = $globals->get('payment_methods');

        $paymentMethodLabel = isset($paymentMethods[$payment_method]) ? $paymentMethods[$payment_method] : '-';

        return array_merge($order->data()->toArray(), [
            'id' => $order->id(),
            'order_items' => $mappedItems,
            'payment_method_label' => $paymentMethodLabel,
        ]);
    }

    /**
     * Helper: Get last order
     */
    private function getLastOrder()
    {
        if (!session()->has('last_order_id')) {
            return null;
        }

        $order = Entry::find(session('last_order_id'));

        if (!$order || $order->collection()->handle() !== 'orders') {
            return null;
        }

        return $order;
    }
}
