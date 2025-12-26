<?php
/* --------------------------------------------------------------
   ListingItemPriceInformation.php 2022-08-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\Model\ValueObjects\InternalToBeRenamed;

use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemPriceStatus;

/**
 * Class ListingItemPriceInformation
 *
 * @package Gambio\Shop\Modules\ProductListing\Model\ValueObjects
 */
class ListingItemPriceInformation
{
    private float                  $basePrice;
    private float                  $taxRate;
    private ListingItemPriceStatus $priceStatus;
    
    
    /**
     * ListingItemPriceInformation constructor.
     *
     * @param float                  $basePrice
     * @param float                  $taxRate
     * @param ListingItemPriceStatus $priceStatus
     */
    public function __construct(float $basePrice, float $taxRate, ListingItemPriceStatus $priceStatus)
    {
        $this->basePrice   = $basePrice;
        $this->taxRate     = $taxRate;
        $this->priceStatus = $priceStatus;
    }
    
    
    /**
     * @return float
     */
    public function basePrice(): float
    {
        return $this->basePrice;
    }
    
    
    /**
     * @return float
     */
    public function taxRate(): float
    {
        return $this->taxRate;
    }
    
    
    /**
     * @return bool
     */
    public function isPriceStatusNormal(): bool
    {
        return $this->priceStatus->isNormal();
    }
    
    
    /**
     * @return bool
     */
    public function isPriceStatusPriceOnRequest(): bool
    {
        return $this->priceStatus->isPriceOnRequest();
    }
    
    
    /**
     * @return bool
     */
    public function isPriceStatusNotAvailableForPurchase(): bool
    {
        return $this->priceStatus->isNotAvailableForPurchase();
    }
}
