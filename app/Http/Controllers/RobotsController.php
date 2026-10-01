<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

/**
 * /robots.txt — dynamic so the Sitemap line is an absolute URL on whichever
 * domain is serving it (the spec requires absolute), and so non-production
 * environments (staging, local) stay out of search indexes entirely.
 */
class RobotsController extends Controller
{
    public function __invoke(): Response
    {
        $lines = app()->isProduction()
            ? ['User-agent: *', 'Disallow: /admin', '', 'Sitemap: '.route('sitemap')]
            : ['User-agent: *', 'Disallow: /'];

        return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
