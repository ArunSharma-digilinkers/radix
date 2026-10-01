<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DealerController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InfrastructureController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

/*
 | Public routes.
 |
 | URLs must stay clean and human-readable — never query strings like ?row=31.
 | The full sitemap is built out in Phase 4; see docs/PROJECT_PLAN.md.
 */

Route::get('/', HomeController::class)->name('home');
Route::view('/about', 'pages.about')->name('about');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'enquire'])->name('contact.enquire');
Route::get('/dealers', DealerController::class)->name('dealers.index');
Route::get('/careers', [CareerController::class, 'index'])->name('careers.index');
Route::post('/careers/apply', [CareerController::class, 'apply'])->name('careers.apply');
Route::get('/export', [ExportController::class, 'index'])->name('export.index');
Route::post('/export/enquire', [ExportController::class, 'enquire'])->name('export.enquire');
Route::get('/infrastructure', [InfrastructureController::class, 'index'])->name('infrastructure.index');

/*
 | Products. The category route is declared before /products/{product} for
 | the same reason as blog/category below — a slug parameter would otherwise
 | swallow it.
 */
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/category/{category}', [ProductController::class, 'category'])->name('products.category');
Route::get('/products/{product}/documents/{document}', [ProductController::class, 'downloadDocument'])->name('products.documents.download');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

/*
 | Blog. The category route is declared before /blog/{post} so that
 | /blog/category/... is never swallowed by the post's slug parameter.
 */
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/category/{category}', [BlogController::class, 'category'])->name('blog.category');
Route::get('/blog/{post}', [BlogController::class, 'show'])->name('blog.show');

/*
 | Living style guide (brief §9).
 |
 | Kept out of production so it is never indexed and never leaks internal notes;
 | it is a build tool, not a page of the site.
 */
if (! app()->isProduction()) {
    Route::view('/styleguide', 'pages.styleguide')->name('styleguide');
}
