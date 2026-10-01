<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Models\ExportMarket;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Export page (brief §4): the interactive market map, per-region blurbs,
 * the logistics overview, and a partner enquiry form.
 *
 * The partner form is persisted — like the Career application form, and
 * unlike Contact's — because Enquiry::TYPE_EXPORT and the existing
 * Livewire\Admin\Enquiries\Inbox already exist for exactly this; there is no
 * new admin surface to build to make submissions visible.
 */
class ExportController extends Controller
{
    public function index(): View
    {
        return view('pages.export', [
            'markets' => ExportMarket::forDisplay()->get(),
        ]);
    }

    public function enquire(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:32'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        Enquiry::create([
            'type' => Enquiry::TYPE_EXPORT,
            'name' => $validated['name'],
            'company' => $validated['company'] ?? null,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'message' => $validated['message'],
            'status' => Enquiry::STATUS_NEW,
            'source_url' => url()->previous(),
            'referrer' => $request->headers->get('referer'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect(route('export.index').'#enquire')->with('enquired', true);
    }
}
