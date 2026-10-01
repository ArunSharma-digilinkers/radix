<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

/**
 * Contact page (brief §4): quick enquiry form with a product dropdown.
 *
 * Product pages link here with ?product=<slug>, which pre-selects the
 * dropdown and the "Product enquiry" reason. Submissions land in the same
 * Enquiry inbox as the export form.
 */
class ContactController extends Controller
{
    public function index(Request $request): View
    {
        $products = $this->products();
        $slug = $request->query('product');

        return view('pages.contact', [
            'products' => $products,
            'selectedProduct' => $products->has($slug) ? $slug : null,
        ]);
    }

    public function enquire(Request $request): RedirectResponse
    {
        $products = Product::forDisplay()->get()->keyBy('slug');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:32'],
            'type' => ['required', Rule::in(Enquiry::TYPES)],
            'product' => ['nullable', Rule::in($products->keys()->all())],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        Enquiry::create([
            'type' => $validated['type'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'message' => $validated['message'],
            'product_id' => $products->get($validated['product'] ?? null)?->id,
            'status' => Enquiry::STATUS_NEW,
            'source_url' => url()->previous(),
            'referrer' => $request->headers->get('referer'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect(route('contact').'#enquiry')->with('enquired', true);
    }

    /** @return Collection<string, string> slug => display name */
    private function products()
    {
        return Product::forDisplay()->get()
            ->mapWithKeys(fn (Product $product) => [$product->slug => $product->getTranslation('name', 'en')]);
    }
}
