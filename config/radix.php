<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Contact channels
    |--------------------------------------------------------------------------
    |
    | Left null until Radix confirms current details (brief §11.5). A null value
    | makes the corresponding UI omit itself rather than render a dead link or a
    | placeholder number that could reach production.
    |
    | These move into the site_settings table in Phase 3 so the team can edit
    | them without a deploy; config is the interim home.
    |
    */

    'whatsapp' => env('RADIX_WHATSAPP'),

    'toll_free' => env('RADIX_TOLL_FREE'),

];
