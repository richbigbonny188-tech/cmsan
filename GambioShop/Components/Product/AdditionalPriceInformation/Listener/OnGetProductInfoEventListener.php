<?php
/*--------------------------------------------------------------
   OnGetProductInfoEventListener.php 2020-06-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Product\AdditionalPriceInformation\Listener;

use Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces\OnGetProductInfoEventInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\ShowAdditionalPriceInformation;

/**
 * Class OnGetProductInfoEventListener
 * @package Gambio\Shop\Product\AdditionalPriceInformation\Listener
 */
class OnGetProductInfoEventListener
{
    /**
     * @param OnGetProductInfoEventInterface $event
     */
    public function __invoke(OnGetProductInfoEventInterface $event)
    {
        $showAdditionalInformation = new ShowAdditionalPriceInformation($event->product()->showPropertyPrice());
        
        $event->builder()->withShowAdditionalPriceInformation($showAdditionalInformation);
    }
}