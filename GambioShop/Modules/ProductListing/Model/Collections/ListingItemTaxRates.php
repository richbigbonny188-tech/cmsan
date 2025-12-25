<?php
/* --------------------------------------------------------------
   ListingItemTaxRates.php 2022-05-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\Model\Collections;

use ArrayIterator;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemTaxRate;
use IteratorAggregate;

/**
 * Class ListingItemTaxRates
 *
 * @package Gambio\Shop\Modules\ProductListing\Model\Collections
 */
class ListingItemTaxRates implements IteratorAggregate
{
    /**
     * @var ListingItemTaxRate[]
     */
    private array $rates;
    
    
    /**
     * ListingItemTaxRates constructor.
     *
     * @param ListingItemTaxRate ...$rates
     */
    public function __construct(ListingItemTaxRate ...$rates)
    {
        $this->rates = $rates;
    }
    
    
    /**
     * Array serialization.
     *
     * @return void
     */
    public function toArray(): array
    {
        $cb = static function (ListingItemTaxRate $rate): array {
            return $rate->toArray();
        };
        
        return array_map($cb, $this->rates);
    }
    
    
    /**
     * @inheritDoc
     * @return ListingItemTaxRate[]|iterable
     */
    public function getIterator(): iterable
    {
        return new ArrayIterator($this->rates);
    }
}