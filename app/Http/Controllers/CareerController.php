<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\JobOpening;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Careers page (brief §4): the open positions list, or a "send your CV"
 * fallback when there are none — see JobOpening::scopeOpen() and the
 * job_applications table's nullable job_opening_id, both built for exactly
 * this.
 *
 * Applications ARE persisted (unlike the Contact and dealer forms, which stay
 * markup-only pending the rest of the Phase 5 lead pipeline) so that every
 * submission reaches the admin inbox — see Livewire\Admin\Careers\Applications.
 * What Phase 5 still owns: spam protection, auto-acknowledgement email, and
 * CRM routing.
 */
class CareerController extends Controller
{
    public function index(): View
    {
        return view('pages.careers', [
            'openings' => JobOpening::open()->orderBy('published_at', 'desc')->get(),
        ]);
    }

    public function apply(Request $request): RedirectResponse
    {
        $openSlugs = JobOpening::open()->pluck('slug');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:32'],
            'job_opening_slug' => ['nullable', 'string', Rule::in($openSlugs)],
            'cover_note' => ['nullable', 'string', 'max:5000'],
            'resume' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
        ]);

        $opening = $validated['job_opening_slug'] ?? null
            ? JobOpening::where('slug', $validated['job_opening_slug'])->first()
            : null;

        $application = JobApplication::create([
            'job_opening_id' => $opening?->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'cover_note' => $validated['cover_note'] ?? null,
            'status' => JobApplication::STATUS_NEW,
        ]);

        // Resumes carry personal data, so they go on the private 'local' disk
        // (see JobApplication's $hidden) rather than 'public' — never a URL
        // anyone can guess. Keyed by the application's own id, which only
        // exists once the row above is saved.
        $resume = $request->file('resume');
        $path = $resume->storeAs("job-applications/{$application->id}", $resume->getClientOriginalName(), 'local');
        $application->update(['resume_disk' => 'local', 'resume_path' => $path]);

        return redirect(route('careers.index').'#apply')->with('applied', true);
    }
}
