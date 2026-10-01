<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Response;

/**
 * /sitemap.xml — every public, indexable URL.
 *
 * Built from the same scopes the public pages use (forDisplay / published), so
 * a hidden product or draft post can never be advertised to crawlers.
 * Dynamic rows carry <lastmod>; static pages don't, because claiming a date
 * we can't know would be worse than omitting it.
 */
class SitemapController extends Controller
{
    private const STATIC_ROUTES = [
        'home', 'about', 'products.index', 'infrastructure.index', 'export.index',
        'careers.index', 'blog.index', 'dealers.index', 'contact',
    ];

    public function __invoke(): Response
    {
        $urls = collect(self::STATIC_ROUTES)
            ->map(fn (string $name) => ['loc' => route($name), 'lastmod' => null]);

        $urls = $urls
            ->concat(ProductCategory::forDisplay()->get()->map(fn ($c) => [
                'loc' => route('products.category', $c), 'lastmod' => $c->updated_at,
            ]))
            ->concat(Product::forDisplay()->get()->map(fn ($p) => [
                'loc' => route('products.show', $p), 'lastmod' => $p->updated_at,
            ]))
            ->concat(PostCategory::forDisplay()->get()->map(fn ($c) => [
                'loc' => route('blog.category', $c), 'lastmod' => $c->updated_at,
            ]))
            ->concat(Post::published()->latestFirst()->get()->map(fn ($p) => [
                'loc' => route('blog.show', $p), 'lastmod' => $p->updated_at,
            ]));

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
