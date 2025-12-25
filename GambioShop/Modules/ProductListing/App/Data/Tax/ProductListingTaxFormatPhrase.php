<?php
/* --------------------------------------------------------------
   ProductListingTaxFormatPhrase.php 2022-05-17
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
 * Class ProductListingTaxFormatPhrase
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data
 */
class ProductListingTaxFormatPhrase
{
    private ProductListingTaxFormatTranslationFactory $translationFactory;
    
    
    /**
     * ProductListingTaxFormatPhrase constructor.
     *
     * @param ProductListingTaxFormatTranslationFactory $translationFactory
     */
    public function __construct(ProductListingTaxFormatTranslationFactory $translationFactory)
    {
        $this->translationFactory = $translationFactory;
    }
    
    
    /**
     * Returns phrase for formatted tax rate.
     *
     * @param float           $taxRate
     * @param ListingSettings $listingSettings
     *
     * @return string
     */
    public function getPhrase(float $taxRate, ListingSettings $listingSettings): string
    {
        return $this->translationFactory->create($listingSettings)->getPhrase($taxRate);
    }
}