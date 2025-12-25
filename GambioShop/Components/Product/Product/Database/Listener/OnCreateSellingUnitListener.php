<?php
/*--------------------------------------------------------------------------------------------------
    OnCreateSellingUnitListener.php 2020-07-17
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\Product\Product\Database\Listener;

use Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces\OnCreateSellingUnitEventInterface;
use MainFactory;
use ProductDataInterface;

/**
 * Class OnCreateSellingUnitListener
 * @package Gambio\Shop\Product\Product\Database\Listener
 */
class OnCreateSellingUnitListener
{
    /**
     * @param OnCreateSellingUnitEventInterface $event
     *
     * @return OnCreateSellingUnitEventInterface
     */
    public function __invoke(OnCreateSellingUnitEventInterface $event): OnCreateSellingUnitEventInterface
    {
        // @codeCoverageIgnoreStart
        if ($event->product() === null) {
            /** @var ProductDataInterface $product */
            $product = MainFactory::create('product',
                                           $event->id()->productId()->value(),
                                           $event->id()->language()->value());
            $event->setProduct($product);
        }
        // @codeCoverageIgnoreEnd
        
        return $event;
    }
    
}