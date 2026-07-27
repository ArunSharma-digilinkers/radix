<?php

use Illuminate\Support\Facades\Route;

/*
 | Public routes.
 |
 | URLs must stay clean and human-readable — never query strings like ?row=31.
 | The full sitemap is built out in Phase 4; see docs/PROJECT_PLAN.md.
 */

Route::view('/', 'pages.home')->name('home');

/*
 | Living style guide (brief §9).
 |
 | Kept out of production so it is never indexed and never leaks internal notes;
 | it is a build tool, not a page of the site.
 */
if (! app()->isProduction()) {
    Route::view('/styleguide', 'pages.styleguide')->name('styleguide');
}
