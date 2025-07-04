<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Statamic\Entries\Entry;
use Statamic\Facades\Collection;

class ReviewController extends Controller
{
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'product' => 'required|string',
            'customer' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'message' => 'required|string',
        ]);

        $entry = Entry::make()
            ->collection('reviews')
            ->data([
                'title' => $validated['name'] . '( '. $validated['email'] .' )',
                'name' => $validated['name'],
                'email' => $validated['email'],
                'product' => $validated['product'],
                'customer' => $validated['customer'],
                'rating' => $validated['rating'],
                'message' => $validated['message'],
                'created_at' => now(),
            ])
            ->save();

        // Fetch all reviews for this product
        $reviews = Entry::query()
            ->where('collection', 'reviews')
            ->where('product', $validated['product'])
            ->get();

        // Calculate average rating
        $averageRating = round(
            $reviews->map(function ($review) {
                return $review->get('rating'); // Convert LabeledValue to int
            })->avg(), 2);

        // Update the product entry
        $product = Entry::find($validated['product']);

        if ($product) {
            $product->set('average_rating', $averageRating);
            $product->save();
        }

        return response()->json(['success' => true]);
    }

}
