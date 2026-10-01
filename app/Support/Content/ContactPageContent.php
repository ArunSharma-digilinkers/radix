<?php

namespace App\Support\Content;

use App\Models\Enquiry;

/**
 * Contact page content.
 *
 * PHASE 1-STYLE SCAFFOLD — same seam as {@see HomePageContent}. The enquiry
 * form itself is markup only, same as the homepage's dealer-search and
 * battery-finder forms: submission handling, validation and spam protection
 * land with the rest of the lead pipeline in Phase 5.
 *
 * Regional office addresses are intentionally absent: config/radix.php's
 * whatsapp/toll_free values are null until Radix confirms current contact
 * details (brief §11.5), and office addresses fall under the same
 * unconfirmed-details flag. The template hides the section while this stays
 * empty rather than publish a guessed address.
 */
class ContactPageContent
{
    /**
     * Enquiry reason options for the contact form select field. Values are
     * the real {@see Enquiry::TYPES} constants, not invented — only the
     * display labels are added here.
     *
     * @return array<string, string>
     */
    public static function enquiryTypes(): array
    {
        return [
            Enquiry::TYPE_GENERAL => 'General enquiry',
            Enquiry::TYPE_PRODUCT => 'Product enquiry',
            Enquiry::TYPE_DEALER => 'Become a dealer',
            Enquiry::TYPE_EXPORT => 'Export / B2B enquiry',
            Enquiry::TYPE_CAREER => 'Careers',
        ];
    }

    /**
     * Regional/head offices. Empty until Radix confirms current addresses.
     *
     * @return list<array{name: string, address: string}>
     */
    public static function offices(): array
    {
        return [];
    }
}
