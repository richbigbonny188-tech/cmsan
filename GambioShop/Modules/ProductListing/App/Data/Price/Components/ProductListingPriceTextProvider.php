<?php
/* --------------------------------------------------------------
   ProductListingPriceTextProvider.php 2023-05-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Data\Price\Components;

use Gambio\Core\TextManager\Services\TextManager;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSettings;

/**
 * Class ProductListingPriceTextProvider
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data\Price
 */
class ProductListingPriceTextProvider
{
    private TextManager $textManager;
    
    
    /**
     * ProductListingPriceTextProvider constructor.
     *
     * @param TextManager $textManager
     */
    public function __construct(TextManager $textManager)
    {
        $this->textManager = $textManager;
    }
    
    
    /**
     * Provides text for customers that are in a group which is not allowed to see prices.
     *
     * @param ListingSettings $listingSettings
     *
     * @return string
     */
    public function notAllowedToSeePrices(ListingSettings $listingSettings): string
    {
        return $this->translate('NOT_ALLOWED_TO_SEE_PRICES', 'general', $listingSettings);
    }
    
    
    /**
     * Provides text for variant.
     *
     * @param ListingSettings $listingSettings
     *
     * @return string
     */
    public function variant(ListingSettings $listingSettings): string
    {
        return $this->translateAndLcFirstCharsOfWords('FROM', 'general', $listingSettings);
    }
    
    
    /**
     * Provides text for variant with special offer.
     *
     * @param ListingSettings $listingSettings
     *
     * @return string
     */
    public function variantWithSpecial(ListingSettings $listingSettings): string
    {
        return $this->translate('from_only', 'price', $listingSettings);
    }
    
    
    /**
     * Provides text for special offer.
     *
     * @param ListingSettings $listingSettings
     *
     * @return string
     */
    public function specialOffer(ListingSettings $listingSettings): string
    {
        return $this->translate('new_special_price', 'price', $listingSettings);
    }
    
    
    /**
     * Provides text for price without special offer.
     *
     * @param ListingSettings $listingSettings
     *
     * @return string
     */
    public function specialOfferBefore(ListingSettings $listingSettings): string
    {
        return $this->translate('old_special_price', 'price', $listingSettings);
    }
    
    
    /**
     * Provides text for discount.
     *
     * @param ListingSettings $listingSettings
     *
     * @return string
     */
    public function discount(ListingSettings $listingSettings): string
    {
        return $this->translate('new_discount_price', 'price', $listingSettings);
    }
    
    
    /**
     * Provides text for price without discount.
     *
     * @param ListingSettings $listingSettings
     *
     * @return string
     */
    public function discountPreviousPrice(ListingSettings $listingSettings): string
    {
        return $this->translate('old_discount_price', 'price', $listingSettings);
    }
    
    
    /**
     * Provides text for price saving.
     *
     * @param ListingSettings $listingSettings
     *
     * @return string
     */
    public function discountSaving(ListingSettings $listingSettings): string
    {
        return $this->translate('you_save', 'price', $listingSettings);
    }
    
    
    /**
     * Provides text for personal offer.
     *
     * @param ListingSettings $listingSettings
     *
     * @return string
     */
    public function personalOffer(ListingSettings $listingSettings): string
    {
        return $this->translate('new_group_price', 'price', $listingSettings);
    }
    
    
    /**
     * Provides text for price without personal offer.
     *
     * @param ListingSettings $listingSettings
     *
     * @return string
     */
    public function personalOfferBefore(ListingSettings $listingSettings): string
    {
        return $this->translate('old_group_price', 'price', $listingSettings);
    }
    
    
    /**
     * Provides text to show product prices on request only.
     *
     * @param ListingSettings $listingSettings
     *
     * @return string
     */
    public function showPriceOnRequest(ListingSettings $listingSettings): string
    {
        return $this->translate('GM_SHOW_PRICE_ON_REQUEST', 'general', $listingSettings);
    }
    
    
    /**
     * Provides text that product is not available for purchase.
     *
     * @param ListingSettings $listingSettings
     *
     * @return string
     */
    public function isNotAvailableForPurchase(ListingSettings $listingSettings): string
    {
        return $this->translate('GM_SHOW_NO_PRICE', 'general', $listingSettings);
    }
    
    
    /**
     * Utility method to pass arguments into the text manager.
     *
     * @param string          $phrase
     * @param string          $section
     * @param ListingSettings $listingSettings
     *
     * @return string
     */
    private function translate(string $phrase, string $section, ListingSettings $listingSettings): string
    {
        return $this->textManager->getPhraseText($phrase, $section, $listingSettings->languageId());
    }
    
    
    /**
     * Same as ProductListingPriceTextProvider::translate, but takes case to lower case
     * first characters of each word in the translation.
     *
     * @param string          $phrase
     * @param string          $section
     * @param ListingSettings $listingSettings
     *
     * @return string
     * @see ProductListingPriceTextProvider::translate
     */
    private function translateAndLcFirstCharsOfWords(
        string          $phrase,
        string          $section,
        ListingSettings $listingSettings
    ): string {
        $translation = $this->translate($phrase, $section, $listingSettings);
        
        $separator        = ' ';
        $translationWords = explode($separator, $translation);
        $translationWords = array_map('lcfirst', $translationWords);
        
        return implode($separator, $translationWords);
    }
}
