<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Statamic\Facades\Entry;
use Statamic\View\View;
use Illuminate\Support\Str;
use Statamic\Facades\Term;

class BlogController extends Controller
{
    public function list(Request $request)
    {
        $categoryTitle = null;
        $categoryDescription = null;

        $tagTitle = null;
        $tagDescription = null;

        $query = Entry::query()
            ->where('collection', 'blogs')
            ->where('published', true);

        if ($search = $request->get('s')) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($category = $request->get('category')) {
            if (is_string($category) && !empty($category)) {
                $query->whereJsonContains('blog_categories', $category);

                $term = Term::query()
                    ->where('taxonomy', 'blog_categories')
                    ->where('slug', $category)
                    ->first();

                if ($term) {
                    $categoryTitle = $term->title();
                    $categoryDescription = $term->get('description');
                }
            }
        }

        if ($tag = $request->get('tag')) {
            if (is_string($tag) && !empty($tag)) {
                $query->whereJsonContains('blog_tags', $tag);

                $term = Term::query()
                    ->where('taxonomy', 'blog_tags')
                    ->where('slug', $tag)
                    ->first();

                if ($term) {
                    $tagTitle = $term->title();
                    $tagDescription = $term->get('description');
                }
            }
        }

        $blogs = $query->orderBy('date', 'desc')->paginate(6)->withQueryString();

        // Transform to add simple arrays for easier antler handling
        $blogs->getCollection()->transform(function ($blog) {
            $blog->blog_categories = $blog->blog_categories ? $blog->blog_categories->toArray() : [];
            $blog->blog_tags = $blog->blog_tags ? $blog->blog_tags->toArray() : [];
            
            return $blog;
        });

        // $html = view('partials.blog-list-partial', ['blogs' => $blogs])->render();
        $html = View::make('partials.blog-list-partial', ['blogs' => $blogs, 'blogs_total' => $blogs->total()])->render();

        return response()->json([
            'html' => $html,
            'blogs' => $blogs,
            'category_title' => $categoryTitle,
            'category_description' => $categoryDescription,
            'tag_title' => $tagTitle,
            'tag_description' => $tagDescription,
        ]);
    }

}
