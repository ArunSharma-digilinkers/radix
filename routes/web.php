<?php

use Illuminate\Support\Facades\Route;

/*
 | Public routes.
 |
 | URLs must stay clean and human-readable — never query strings like ?row=31.
 | The full sitemap is built out in Phase 4; see docs/PROJECT_PLAN.md.
 */

Route::view('/', 'pages.home')->name('home');
