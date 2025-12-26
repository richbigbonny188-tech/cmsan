<?php
/* --------------------------------------------------------------
   ListingItemPriceSpecial.php 2022-08-05
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
 * Class ListingItemPriceSpecial
 *
 * @package Gambio\Shop\Modules\ProductListing\Model\ValueObjects
 */
class ListingItemPriceSpecial implements ListingItemPriceExtraInfo
{
    public const EXTRA_KIND = 'special';
    
    private float  $normalPrice;
    private string $formatted;
    
    
    /**
     * ListingItemPriceSpecial constructor.
     *
     * @param float  $normalPrice
     * @param string $formatted
     */
    public function __construct(float $normalPrice, string $formatted)
    {
        $this->normalPrice = $normalPrice;
        $this->formatted   = $formatted;
    }
    
    
    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'normalPrice' => $this->normalPrice,
            'formatted'   => $this->formatted,
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