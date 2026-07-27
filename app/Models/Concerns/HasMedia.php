<?php

namespace App\Models\Concerns;

use App\Models\Media;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * Attaches polymorphic media to a model.
 *
 * Everything with a picture uses this rather than its own path column, so upload
 * handling, alt text and responsive derivatives are implemented once.
 */
trait HasMedia
{
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
    }

    /**
     * The single lead image for a model — hero, portrait, thumbnail.
     */
    public function image(): MorphOne
    {
        return $this->morphOne(Media::class, 'mediable')
            ->where('collection', Media::COLLECTION_MAIN)
            ->orderBy('sort_order');
    }

    public function gallery(): MorphMany
    {
        return $this->media()->where('collection', Media::COLLECTION_GALLERY);
    }
}
