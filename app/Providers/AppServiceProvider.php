<?php

namespace App\Providers;

use App\Support\Content\SiteContent;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->composeSiteChrome();
    }

    /**
     * Header, footer and contact details are identical on every public page, so
     * they are bound to the layout rather than passed by each controller.
     *
     * This composer is the swap point for Phase 4: when navigation and settings
     * move into the database, only this method changes — no Blade template does.
     */
    private function composeSiteChrome(): void
    {
        View::composer('components.layouts.public', function ($view) {
            $view->with([
                'nav' => SiteContent::nav(),
                'footerColumns' => SiteContent::footerColumns(),
                'footerBlurb' => SiteContent::footerBlurb(),
                'certifications' => SiteContent::certifications(),
                'whatsapp' => SiteContent::whatsapp(),
            ]);
        });
    }
}
