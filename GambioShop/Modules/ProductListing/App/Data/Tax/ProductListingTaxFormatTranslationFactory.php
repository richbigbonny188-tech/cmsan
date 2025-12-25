<?php
/* --------------------------------------------------------------
   ProductListingTaxFormatTranslationFactory.php 2022-05-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Data\Tax;

use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSettings;

/**
 * Class ProductListingTaxFormatTranslationFactory
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data
 */
class ProductListingTaxFormatTranslationFactory
{
    private ProductListingTaxFormatSettings $settings;
    
    
    /**
     * ProductListingTaxFormatTranslationFactory constructor.
     *
     * @param ProductListingTaxFormatSettings $settings
     */
    public function __construct(ProductListingTaxFormatSettings $settings)
    {
        $this->settings = $settings;
    }
    
    
    /**
     * Creates product listing tax format translations with applied settings.
     *
     * @param ListingSettings $listingSettings
     *
     * @return ProductListingTaxFormatTranslation
     */
    public function create(ListingSettings $listingSettings): ProductListingTaxFormatTranslation
    {
        $customerId = $listingSettings->customerId();
        $languageId = $listingSettings->languageId();
        
        return new ProductListingTaxFormatTranslation($this->settings->isTaxFree(),
                                                      $this->settings->showTax(),
                                                      $this->settings->showEmptyTax(),
                                                      $this->settings->showPriceTax($customerId, $languageId),
                                                      $this->settings->addTax($customerId, $languageId));
    }
}