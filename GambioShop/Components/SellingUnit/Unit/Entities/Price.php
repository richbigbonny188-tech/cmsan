<?php
/*--------------------------------------------------------------------
 Price.php 2020-2-17
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/
namespace Gambio\Shop\SellingUnit\Unit\Entities;

use Gambio\Shop\SellingUnit\Unit\ValueObjects\PriceFormatted;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\PricePlain;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\PriceStatus;

/**
 * Class Price
 * @package Gambio\Shop\SellingUnit\Unit\Entities
 */
class Price
{
    /**
     * @var PricePlain
     */
    protected $pricePlain;
    
    /**
     * @var PriceFormatted
     */
    protected $formattedPrice;
    /**
     * @var PriceStatus
     */
    protected $status;


    /**
     * Price constructor.
     *
     * @param PricePlain $pricePlain
     * @param PriceFormatted $formattedPrice
     * @param PriceStatus $status
     */
    public function __construct(PricePlain $pricePlain, PriceFormatted $formattedPrice, PriceStatus $status)
    {
        $this->pricePlain     = $pricePlain;
        $this->formattedPrice = $formattedPrice;
        $this->status = $status;
    }
    
    
    /**
     * @return PricePlain
     */
    public function pricePlain(): PricePlain
    {
        return $this->pricePlain;
    }
    
    
    /**
     * @return PriceFormatted
     */
    public function formattedPrice(): PriceFormatted
    {
        return $this->formattedPrice;
    }

    /**
     * @return PriceStatus
     */
    public function status(): PriceStatus
    {
        return $this->status;
    }
}