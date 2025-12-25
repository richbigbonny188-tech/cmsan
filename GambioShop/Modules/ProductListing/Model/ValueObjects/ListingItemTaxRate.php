<?php
/* --------------------------------------------------------------
   ListingItemTaxRate.php 2022-05-11
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
 * Class ListingItemTaxRate
 *
 * @package Gambio\Shop\Modules\ProductListing\Model\ValueObjects
 */
class ListingItemTaxRate
{
    private string $description;
    private float  $rate;
    private int    $priority;
    
    
    /**
     * ListingItemTaxRate constructor.
     *
     * @param string $description
     * @param float  $rate
     * @param int    $priority
     */
    public function __construct(string $description, float $rate, int $priority)
    {
        $this->description = $description;
        $this->rate        = $rate;
        $this->priority    = $priority;
    }
    
    
    /**
     * @return float
     */
    public function rate(): float
    {
        return $this->rate;
    }
    
    
    /**
     * @return int
     */
    public function priority(): int
    {
        return $this->priority;
    }
    
    
    /**
     * Array serialization.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'description' => $this->description,
            'rate'        => $this->rate,
            'priority'    => $this->priority,
        ];
    }
}