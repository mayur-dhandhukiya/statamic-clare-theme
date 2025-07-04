<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Statamic\Facades\Entry;
use Statamic\View\View;
use Statamic\Facades\Term;
use Illuminate\Support\Str;
use \Illuminate\Pagination\LengthAwarePaginator;

class ProductController extends Controller
{
    public function list(Request $request)
    {
        $categoryTitle = null;
        $categoryDescription = null;

        $tagTitle = null;
        $tagDescription = null;

        $query = Entry::query()
            ->where('collection', 'products')
            ->where('published', true);

        if ($search = $request->get('s')) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($category = $request->get('category')) {
            if (is_string($category) && !empty($category)) {
                $query->whereJsonContains('categories', $category);

                $term = Term::query()
                    ->where('taxonomy', 'categories')
                    ->where('slug', $category)
                    ->first();

                if ($term) {
                    $categoryTitle = $term->title();
                    $categoryDescription = $term->get('description');
                }
            }
        }

        $price_range = $request->get('price_range');
        if ($price_range && $price_range != 'all' ) {
            $query->where(function ($q) use ($price_range) {
                [$min, $max] = explode('-', $price_range);
                $q->orWhere(function ($subQ) use ($min, $max) {
                    $subQ->where('price', '>=', (float) $min);
                    if ($max !== 'max') {
                        $subQ->where('price', '<=', (float) $max);
                    }
                });
            });
        }

        if ($tag = $request->get('tag')) {
            if (is_string($tag) && !empty($tag)) {
                $query->whereJsonContains('product_tags', $tag);

                $term = Term::query()
                    ->where('taxonomy', 'product_tags')
                    ->where('slug', $tag)
                    ->first();

                if ($term) {
                    $tagTitle = $term->title();
                    $tagDescription = $term->get('description');
                }
            }
        }

        $products = $query->get(); // This gets all filtered entries

        // Apply custom sorting in PHP
        if ($sort = $request->get('sorting')) {
            switch ($sort) {
                case 'popularity':
                    $products = $products->sortByDesc(fn($item) => (int) $item->get('popularity') ?? 0);
                    break;
                case 'average_rating':
                    $products = $products->sortByDesc(fn($item) => (float) $item->get('average_rating') ?? 0);
                    break;
                case 'price-asc':
                    $products = $products->sortBy(fn($item) => (float) $item->get('price') ?? 0);
                    break;
                case 'price-desc':
                    $products = $products->sortByDesc(fn($item) => (float) $item->get('price') ?? 0);
                    break;
                default:
                    $products = $products->sortByDesc(fn($item) => $item->date()->timestamp ?? 0);
            }
        } else {
            $products = $products->sortByDesc(fn($item) => $item->date()->timestamp ?? 0);
        }

        $viewType = $request->get('view_type', 'grid-four');
        $page = $request->get('page', 1);

        $perPageOptions = [
            'list' => 4,
            'grid-three' => 6,
            'grid-four' => 8,
        ];

        $perPage = $perPageOptions[$viewType] ?? 8;

        // $products = $query->paginate($perPage)->withQueryString();

        $products = new LengthAwarePaginator(
            $products->forPage($page, $perPage)->values(),
            $products->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $html = View::make('partials.product-list-partial', ['products' => $products, 'products_total' => $products->total(), 'view_type' => $viewType])->render();

        return response()->json([
            'html' => $html,
            'products' => $products,
            'category_title' => $categoryTitle,
            'category_description' => $categoryDescription,
            'tag_title' => $tagTitle,
            'tag_description' => $tagDescription,
            'total' => $products->total(),
            'count' => $products->count(),
        ]);
    }
    
}
