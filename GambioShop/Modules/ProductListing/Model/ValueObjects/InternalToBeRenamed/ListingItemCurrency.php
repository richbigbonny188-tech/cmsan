<?php
/* --------------------------------------------------------------
   ListingItemCurrency.php 2022-08-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\Model\ValueObjects\InternalToBeRenamed;

/**
 * Class ListingItemCurrency
 *
 * @package Gambio\Shop\Modules\ProductListing\Model\ValueObjects
 */
class ListingItemCurrency
{
    private string $currencyCode;
    private float  $value;
    private string $decimalPoint;
    private string $thousandsPoint;
    private int    $decimalPlaces;
    private string $symbolLeft;
    private string $symbolRight;
    
    
    /**
     * ListingItemCurrency constructor.
     *
     * @param string $currencyCode
     * @param float  $value
     * @param string $decimalPoint
     * @param string $thousandsPoint
     * @param int    $decimalPlaces
     * @param string $symbolLeft
     * @param string $symbolRight
     */
    public function __construct(
        string $currencyCode,
        float  $value,
        string $decimalPoint,
        string $thousandsPoint,
        int    $decimalPlaces,
        string $symbolLeft,
        string $symbolRight
    ) {
        $this->currencyCode   = $currencyCode;
        $this->value          = $value;
        $this->decimalPoint   = $decimalPoint;
        $this->thousandsPoint = $thousandsPoint;
        $this->decimalPlaces  = $decimalPlaces;
        $this->symbolLeft     = $symbolLeft;
        $this->symbolRight    = $symbolRight;
    }
    
    
    /**
     * @return string
     */
    public function currencyCode(): string
    {
        return $this->currencyCode;
    }
    
    
    /**
     * @return float
     */
    public function value(): float
    {
        return $this->value;
    }
    
    
    /**
     * @return string
     */
    public function decimalSeparator(): string
    {
        return $this->decimalPoint;
    }
    
    
    /**
     * @return string
     */
    public function thousandsSeparator(): string
    {
        return $this->thousandsPoint;
    }
    
    
    /**
     * @return int
     */
    public function decimalPlaces(): int
    {
        return $this->decimalPlaces;
    }
    
    
    /**
     * @return string
     */
    public function symbolLeft(): string
    {
        return $this->symbolLeft;
    }
    
    
    /**
     * @return string
     */
    public function symbolRight(): string
    {
        return $this->symbolRight;
    }
}