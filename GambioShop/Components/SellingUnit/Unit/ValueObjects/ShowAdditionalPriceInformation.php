<?php
/*--------------------------------------------------------------
   ShowAdditionalPriceInformation.php 2020-06-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Unit\ValueObjects;

/**
 * Class ShowAdditionalPriceInformation
 * @package Gambio\Shop\SellingUnit\Unit\ValueObjects
 */
class ShowAdditionalPriceInformation
{
    /**
     * @var bool
     */
    protected $showPrice;
    
    
    /**
     * ShowAdditionalPriceInformation constructor.
     *
     * @param bool $showPrice
     */
    public function __construct(bool $showPrice)
    {
        $this->showPrice = $showPrice;
    }
    
    
    /**
     * @return bool
     */
    public function value(): bool
    {
        return $this->showPrice;
    }
}