<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductDocument;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Products hub and detail pages (brief §4). The homepage's "category tiles"
 * and About's "what we make" index both link here now instead of the
 * on-page anchor they used before — see HomeController and pages/about.blade.php.
 *
 * Like every other public listing on this site, this reads real Product rows
 * and shows nothing invented: on a fresh install the hub is empty until the
 * admin adds the eight lines through the (already-built) Products admin —
 * same trade-off Blog and Dealers already made.
 */
class ProductController extends Controller
{
    public function index(): View
    {
        return view('pages.products.index', [
            'category' => null,
            'products' => $this->query()->get(),
            'categories' => ProductCategory::forDisplay()->get(),
        ]);
    }

    public function category(ProductCategory $category): View
    {
        abort_unless($category->is_active, 404);

        return view('pages.products.index', [
            'category' => $category,
            'products' => $this->query()->where('product_category_id', $category->id)->get(),
            'categories' => ProductCategory::forDisplay()->get(),
        ]);
    }

    public function show(Product $product): View
    {
        abort_unless($product->is_active, 404);

        $product->load([
            'category',
            'image',
            'gallery',
            'variants' => fn ($query) => $query->where('is_active', true),
            'specs',
            'faqs' => fn ($query) => $query->where('is_active', true),
            'documents' => fn ($query) => $query->where('is_active', true),
            'components',
        ]);

        return view('pages.products.show', ['product' => $product]);
    }

    /**
     * Tracked datasheet/certificate/manual download — download_count exists
     * specifically so the business can see which documents export buyers
     * actually pull before they enquire (see ProductDocument's doc comment).
     * A direct Storage URL would skip that count entirely.
     */
    public function downloadDocument(Product $product, ProductDocument $document): RedirectResponse
    {
        abort_unless($document->product_id === $product->id && $document->is_active, 404);

        $document->increment('download_count');

        return redirect($document->url());
    }

    private function query()
    {
        return Product::forDisplay()->with(['category', 'image']);
    }
}
