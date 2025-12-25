<?php
/* --------------------------------------------------------------
   ListingItemPrice.php 2023-03-28
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
 * Class ListingItemPrice
 *
 * @package Gambio\Shop\Modules\ProductListing\Model\ValueObjects
 */
class ListingItemPrice
{
    private ListingItemPriceValue      $value;
    private string                     $formatted;
    private ?ListingItemPriceExtraInfo $extraInfo;
    
    
    /**
     * ListingItemPrice constructor.
     *
     * @param ListingItemPriceValue          $value
     * @param string                         $formatted
     * @param ListingItemPriceExtraInfo|null $extraInfo
     */
    public function __construct(
        ListingItemPriceValue     $value,
        string                    $formatted,
        ListingItemPriceExtraInfo $extraInfo = null
    ) {
        $this->value     = $value;
        $this->formatted = $formatted;
        $this->extraInfo = $extraInfo;
    }
    
    
    /**
     * Utility factory method creating a price instance with zero as internal value,
     * containing the passed formatted text only.
     *
     * @param string $formatted
     *
     * @return static
     */
    public static function empty(string $formatted): self
    {
        $priceValue = ListingItemPriceValue::empty();
        
        return new static($priceValue, $formatted);
    }
    
    
    /**
     * Array serialization.
     *
     * @return array
     */
    public function toArray(): array
    {
        $data = [
            'finalPrice' => $this->value->value(),
            'formatted'  => $this->formatted,
            'breakdown'  => $this->value->breakdown(),
        ];
        if ($this->extraInfo) {
            $data[$this->extraInfo->kind()] = $this->extraInfo->toArray();
        }
        
        return $data;
    }
}