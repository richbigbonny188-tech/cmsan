<?php
/* --------------------------------------------------------------
   ListingItemPriceDiscount.php 2022-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\Model\ValueObjects;

/**
 * Class ListingItemPriceDiscount
 *
 * @package Gambio\Shop\Modules\ProductListing\Model\ValueObjects
 */
class ListingItemPriceDiscount implements ListingItemPriceExtraInfo
{
    public const EXTRA_KIND = 'discount';
    
    private ListingItemPriceDiscountPreviousPrice $previousPrice;
    private ListingItemPriceDiscountSaving        $saving;
    
    
    /**
     * ListingItemPriceDiscount constructor.
     *
     * @param ListingItemPriceDiscountPreviousPrice $previousPrice
     * @param ListingItemPriceDiscountSaving        $saving
     */
    public function __construct(
        ListingItemPriceDiscountPreviousPrice $previousPrice,
        ListingItemPriceDiscountSaving        $saving
    ) {
        $this->previousPrice = $previousPrice;
        $this->saving        = $saving;
    }
    
    
    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'previousPrice' => $this->previousPrice->toArray(),
            'saving'        => $this->saving->toArray(),
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function kind(): string
    {
        return self::EXTRA_KIND;
    }
}