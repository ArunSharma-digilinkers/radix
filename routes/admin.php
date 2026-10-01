<?php

use App\Http\Controllers\Admin\EditorUploadController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Livewire\Admin\Blog\Categories\Form as BlogCategoryForm;
use App\Livewire\Admin\Blog\Categories\Index as BlogCategoriesIndex;
use App\Livewire\Admin\Blog\Form as BlogForm;
use App\Livewire\Admin\Blog\Index as BlogIndex;
use App\Livewire\Admin\Careers\Applications as CareerApplications;
use App\Livewire\Admin\Careers\Form as CareerForm;
use App\Livewire\Admin\Careers\Index as CareersIndex;
use App\Livewire\Admin\Certifications\Form as CertificationForm;
use App\Livewire\Admin\Certifications\Index as CertificationsIndex;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Dealers\Form as DealerForm;
use App\Livewire\Admin\Dealers\Index as DealersIndex;
use App\Livewire\Admin\Enquiries\Inbox as EnquiriesInbox;
use App\Livewire\Admin\Export\Form as ExportForm;
use App\Livewire\Admin\Export\Index as ExportIndex;
use App\Livewire\Admin\Infrastructure\Gallery as InfrastructureGallery;
use App\Livewire\Admin\Products\Categories\Form as CategoryForm;
use App\Livewire\Admin\Products\Categories\Index as CategoriesIndex;
use App\Livewire\Admin\Products\Form as ProductForm;
use App\Livewire\Admin\Products\Index as ProductsIndex;
use App\Livewire\Admin\Testimonials\Form as TestimonialForm;
use App\Livewire\Admin\Testimonials\Index as TestimonialsIndex;
use App\Livewire\Admin\Users\Form as UserForm;
use App\Livewire\Admin\Users\Index as UsersIndex;
use Illuminate\Support\Facades\Route;

/*
 | Admin auth + panel routes.
 |
 | Plain controllers for login/logout/password-reset (no reactivity needed);
 | Route::livewire() for full-page admin components — required, not just
 | preferred, for Livewire 4 full-page components to work correctly.
 */

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);

    Route::get('/forgot-password', [PasswordResetController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'edit'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])->name('password.update');
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::livewire('/', Dashboard::class)->name('dashboard');

    Route::middleware('can:products.manage')->prefix('products')->name('products.')->group(function () {
        Route::livewire('/', ProductsIndex::class)->name('index');
        Route::livewire('/create', ProductForm::class)->name('create');
        Route::livewire('/{product}/edit', ProductForm::class)->name('edit');

        Route::livewire('/categories', CategoriesIndex::class)->name('categories.index');
        Route::livewire('/categories/create', CategoryForm::class)->name('categories.create');
        Route::livewire('/categories/{category}/edit', CategoryForm::class)->name('categories.edit');
    });

    // Images dropped into a CKEditor body. Gated on media.manage rather than
    // blog.manage: it is a file upload, and the same endpoint serves every
    // rich-text field the admin grows later.
    Route::post('/editor/uploads', [EditorUploadController::class, 'store'])
        ->middleware('can:media.manage')
        ->name('editor.uploads');

    Route::livewire('/enquiries', EnquiriesInbox::class)
        ->middleware('can:enquiries.manage')
        ->name('enquiries.index');

    Route::middleware('can:dealers.manage')->prefix('dealers')->name('dealers.')->group(function () {
        Route::livewire('/', DealersIndex::class)->name('index');
        Route::livewire('/create', DealerForm::class)->name('create');
        Route::livewire('/{dealer}/edit', DealerForm::class)->name('edit');
    });

    Route::middleware('can:careers.manage')->prefix('careers')->name('careers.')->group(function () {
        Route::livewire('/', CareersIndex::class)->name('index');
        Route::livewire('/create', CareerForm::class)->name('create');
        Route::livewire('/{opening}/edit', CareerForm::class)->name('edit');

        Route::livewire('/applications', CareerApplications::class)->name('applications.index');
    });

    Route::middleware('can:export.manage')->prefix('export')->name('export.')->group(function () {
        Route::livewire('/', ExportIndex::class)->name('index');
        Route::livewire('/create', ExportForm::class)->name('create');
        Route::livewire('/{market}/edit', ExportForm::class)->name('edit');
    });

    Route::middleware('can:infrastructure.manage')->group(function () {
        Route::livewire('/infrastructure', InfrastructureGallery::class)->name('infrastructure.index');

        Route::prefix('certifications')->name('certifications.')->group(function () {
            Route::livewire('/', CertificationsIndex::class)->name('index');
            Route::livewire('/create', CertificationForm::class)->name('create');
            Route::livewire('/{certification}/edit', CertificationForm::class)->name('edit');
        });
    });

    Route::middleware('can:testimonials.manage')->prefix('testimonials')->name('testimonials.')->group(function () {
        Route::livewire('/', TestimonialsIndex::class)->name('index');
        Route::livewire('/create', TestimonialForm::class)->name('create');
        Route::livewire('/{testimonial}/edit', TestimonialForm::class)->name('edit');
    });

    Route::middleware('can:blog.manage')->prefix('blog')->name('blog.')->group(function () {
        Route::livewire('/', BlogIndex::class)->name('index');
        Route::livewire('/create', BlogForm::class)->name('create');
        Route::livewire('/{post}/edit', BlogForm::class)->name('edit');

        Route::livewire('/categories', BlogCategoriesIndex::class)->name('categories.index');
        Route::livewire('/categories/create', BlogCategoryForm::class)->name('categories.create');
        Route::livewire('/categories/{category}/edit', BlogCategoryForm::class)->name('categories.edit');
    });

    Route::middleware('can:users.manage')->prefix('users')->name('users.')->group(function () {
        Route::livewire('/', UsersIndex::class)->name('index');
        Route::livewire('/create', UserForm::class)->name('create');
        Route::livewire('/{user}/edit', UserForm::class)->name('edit');
    });
});
