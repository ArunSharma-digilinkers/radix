<?php

namespace Tests\Feature\Models;

use App\Models\Enquiry;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnquiryTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_open_inbox_excludes_closed_leads(): void
    {
        Enquiry::factory()->create(['name' => 'New one']);
        Enquiry::factory()->create(['name' => 'Working', 'status' => Enquiry::STATUS_CONTACTED]);
        Enquiry::factory()->create(['name' => 'Sold', 'status' => Enquiry::STATUS_WON]);
        Enquiry::factory()->create(['name' => 'Gone', 'status' => Enquiry::STATUS_LOST]);

        $this->assertSame(['New one', 'Working'], Enquiry::open()->pluck('name')->all());
    }

    /**
     * The brief promises a reply within one business day, so the inbox has to be
     * able to show what has blown through that.
     */
    public function test_overdue_finds_unanswered_enquiries_past_the_response_window(): void
    {
        Enquiry::factory()->stale()->create(['name' => 'Ignored']);
        Enquiry::factory()->create(['name' => 'Just arrived']);
        Enquiry::factory()->stale()->responded()->create(['name' => 'Handled']);

        $this->assertSame(['Ignored'], Enquiry::overdue()->pluck('name')->all());
    }

    public function test_marking_responded_stamps_the_time_and_advances_a_new_lead(): void
    {
        $enquiry = Enquiry::factory()->create();

        $enquiry->markResponded();

        $this->assertNotNull($enquiry->responded_at);
        $this->assertSame(Enquiry::STATUS_CONTACTED, $enquiry->status);
    }

    public function test_marking_responded_does_not_rewind_a_later_status(): void
    {
        $enquiry = Enquiry::factory()->create(['status' => Enquiry::STATUS_QUALIFIED]);

        $enquiry->markResponded();

        $this->assertSame(Enquiry::STATUS_QUALIFIED, $enquiry->status);
    }

    /**
     * A lead deleted by mistake is lost revenue, so deletes are always soft.
     */
    public function test_enquiries_are_only_ever_soft_deleted(): void
    {
        $enquiry = Enquiry::factory()->create();

        $enquiry->delete();

        $this->assertSoftDeleted($enquiry);
        $this->assertSame(1, Enquiry::withTrashed()->count());
    }

    public function test_a_product_enquiry_remembers_what_was_being_viewed(): void
    {
        $product = Product::factory()->create();

        $enquiry = Enquiry::factory()->ofType(Enquiry::TYPE_PRODUCT)->create([
            'product_id' => $product->id,
        ]);

        $this->assertTrue($enquiry->product->is($product));
    }

    /**
     * Deleting a product must never take its leads with it — the sales history
     * outlives the catalogue entry.
     */
    public function test_removing_a_product_leaves_its_enquiries_behind(): void
    {
        $product = Product::factory()->create();
        $enquiry = Enquiry::factory()->create(['product_id' => $product->id]);

        $product->forceDelete();

        $this->assertNotNull($enquiry->fresh());
        $this->assertNull($enquiry->fresh()->product_id);
    }

    public function test_request_metadata_is_hidden_from_serialisation(): void
    {
        $enquiry = Enquiry::factory()->create([
            'ip_address' => '203.0.113.4',
            'internal_notes' => 'Called twice, no answer',
        ]);

        $array = $enquiry->toArray();

        $this->assertArrayNotHasKey('ip_address', $array);
        $this->assertArrayNotHasKey('user_agent', $array);
        $this->assertArrayNotHasKey('internal_notes', $array);
    }
}
