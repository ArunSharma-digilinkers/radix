<?php

namespace Tests\Feature\Models;

use App\Models\Certification;
use App\Models\Media;
use App\Models\Product;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteSettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_settings_are_read_by_key_with_a_fallback(): void
    {
        SiteSetting::create(['key' => 'distributor_count', 'value' => '650+']);

        $this->assertSame('650+', SiteSetting::get('distributor_count'));
        $this->assertSame('unknown', SiteSetting::get('missing_key', 'unknown'));
    }

    /**
     * Settings are read on nearly every request and written rarely, so they are
     * cached forever and the cache is dropped on write. A stale trust figure on
     * the homepage is exactly the class of bug this project is fixing.
     */
    public function test_the_cache_is_dropped_when_a_setting_changes(): void
    {
        $setting = SiteSetting::create(['key' => 'team_size', 'value' => '150+']);

        $this->assertSame('150+', SiteSetting::get('team_size'));

        $setting->update(['value' => '350+']);

        $this->assertSame('350+', SiteSetting::get('team_size'));
    }

    public function test_deleting_a_setting_also_clears_the_cache(): void
    {
        $setting = SiteSetting::create(['key' => 'toll_free', 'value' => '1800-000-000']);
        SiteSetting::get('toll_free');

        $setting->delete();

        $this->assertNull(SiteSetting::get('toll_free'));
    }

    public function test_media_attaches_polymorphically_to_any_model(): void
    {
        $product = Product::factory()->create();
        $certification = Certification::factory()->create();

        $product->media()->create([
            'collection' => Media::COLLECTION_MAIN,
            'path' => 'products/hero.jpg',
            'filename' => 'hero.jpg',
            'alt' => ['en' => 'A Radix inverter battery'],
        ]);
        $certification->media()->create([
            'collection' => Media::COLLECTION_CERTIFICATE,
            'path' => 'certs/iso.jpg',
            'filename' => 'iso.jpg',
        ]);

        $this->assertCount(1, $product->media);
        $this->assertSame('A Radix inverter battery', $product->image->altText());
        $this->assertTrue($certification->media->first()->mediable->is($certification));
    }

    /**
     * A decorative image should carry an empty alt rather than a filename, which
     * a screen reader would read aloud character by character.
     */
    public function test_missing_alt_text_resolves_to_an_empty_string(): void
    {
        $product = Product::factory()->create();

        $media = $product->media()->create([
            'collection' => Media::COLLECTION_MAIN,
            'path' => 'products/decorative.jpg',
            'filename' => 'decorative.jpg',
        ]);

        $this->assertSame('', $media->altText());
    }

    public function test_expired_certifications_are_excluded_from_the_current_scope(): void
    {
        Certification::factory()->create(['name' => ['en' => 'Valid']]);
        Certification::factory()->neverExpires()->create(['name' => ['en' => 'Perpetual']]);
        $expired = Certification::factory()->expired()->create(['name' => ['en' => 'Lapsed']]);

        $this->assertCount(2, Certification::current()->get());
        $this->assertTrue($expired->hasExpired());
    }
}
