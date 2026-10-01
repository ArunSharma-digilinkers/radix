<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Product;
use App\Models\Testimonial;
use Illuminate\Contracts\View\View;

/**
 * Homepage. Static sections still come from App\Support\Content\HomePageContent
 * (the Phase 1 scaffold); the blog strip, product index and testimonials do
 * not — all three read real records, so the homepage can never advertise an
 * article, a product line, or a named quote that doesn't actually exist
 * (CLAUDE.md §8).
 */
class HomeController extends Controller
{
    /**
     * One lead card plus two in the sidebar — the shape of the approved concept.
     */
    private const POSTS = 3;

    /** One featured quote plus two compact ones, matching the concept's layout. */
    private const TESTIMONIALS = 3;

    public function __invoke(): View
    {
        return view('pages.home', [
            'posts' => Post::published()
                ->with(['category', 'image'])
                ->latestFirst()
                ->limit(self::POSTS)
                ->get(),
            'products' => Product::forDisplay()->with('image')->get(),
            // Featured first (for the large pull-quote slot), then the
            // regular manual order — not forDisplay()'s sort_order-first
            // ordering, which would bury a featured quote behind it.
            'testimonials' => Testimonial::active()
                ->orderByDesc('is_featured')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->limit(self::TESTIMONIALS)
                ->get(),
        ]);
    }
}
