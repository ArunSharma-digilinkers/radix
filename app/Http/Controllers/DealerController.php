<?php

namespace App\Http\Controllers;

use App\Models\Dealer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Public dealer locator (brief §4): search the 650+ network by city, state
 * or PIN code, optionally narrowed by dealer type.
 *
 * This is text search against Dealer::scopeSearch() — a visitor typing "Kanpur"
 * or a PIN gets an anchored match. Turning a free-text location into
 * coordinates so Dealer::scopeNearest() can order by distance is Phase 5
 * (see docs/PROJECT_PLAN.md); until then results are ordered alphabetically.
 */
class DealerController extends Controller
{
    private const PER_PAGE = 12;

    public function __invoke(Request $request): View
    {
        $validated = $request->validate([
            'location' => ['nullable', 'string', 'max:128'],
            'type' => ['nullable', 'string', Rule::in(Dealer::TYPES)],
        ]);

        $dealers = Dealer::active()
            ->search($validated['location'] ?? null)
            ->when($validated['type'] ?? null, fn ($query, string $type) => $query->ofType($type))
            ->orderBy('state')
            ->orderBy('city')
            ->orderBy('name')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        return view('pages.dealers', [
            'dealers' => $dealers,
            'location' => $validated['location'] ?? null,
            'type' => $validated['type'] ?? null,
        ]);
    }
}
