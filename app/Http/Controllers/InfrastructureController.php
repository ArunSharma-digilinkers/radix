<?php

namespace App\Http\Controllers;

use App\Models\Certification;
use App\Models\Media;
use App\Models\SiteSetting;
use Illuminate\Contracts\View\View;

/**
 * Infrastructure page (brief §4): process flow, capacity figure, factory/QC
 * gallery, and certifications with certificate images.
 *
 * The gallery, capacity figure and certifications are the exact items
 * CLAUDE.md §8 blocks pending client input ("current factory photos,
 * machinery, capacity figures... new certifications"). Each is wired to real
 * data — see App\Models\SiteSetting::galleryOwner() and App\Models\Certification
 * — and renders nothing beyond an honest empty state until Radix supplies it
 * through the admin, same treatment as every other unverified section on
 * this site.
 */
class InfrastructureController extends Controller
{
    public function index(): View
    {
        return view('pages.infrastructure', [
            'gallery' => SiteSetting::galleryOwner()->media()->where('collection', Media::COLLECTION_FACTORY)->get(),
            'capacity' => SiteSetting::get('infrastructure_capacity'),
            'certifications' => Certification::current()->forDisplay()->get(),
        ]);
    }
}
