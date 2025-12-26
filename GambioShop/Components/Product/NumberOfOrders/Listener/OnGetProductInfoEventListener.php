<?php
/* --------------------------------------------------------------
  OnGetProductInfoEventListener.php 2020-02-18
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Product\NumberOfOrders\Listener;

use Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces\OnGetProductInfoEventInterface;
use Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces\OnGetProductNumberOfOrdersEventInterface;
use Gambio\Shop\SellingUnit\Database\Unit\Events\OnGetProductNumberOfOrdersEvent;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\NumberOfOrders;

/**
 * Class OnGetProductInfoEventListener
 * @package Gambio\Shop\Product\NumberOfOrders\Listener
 */
class OnGetProductInfoEventListener
{
    /**
     * @param OnGetProductInfoEventInterface $event
     */
    public function __invoke(OnGetProductInfoEventInterface $event)
    {
        $product        = $event->product();
        $numberOfOrders = $this->numberOfOrders($product->getNumberOfOrders());
        
        $event->builder()->withNumberOfOrders($numberOfOrders);
        $event->dispatcher()->dispatch($this->createOnGetProductNumberOfOrdersEvent($event));
    }
    
    
    /**
     * @param OnGetProductInfoEventInterface $event
     *
     * @return OnGetProductNumberOfOrdersEventInterface
     */
    protected function createOnGetProductNumberOfOrdersEvent(OnGetProductInfoEventInterface $event
    ) : OnGetProductNumberOfOrdersEventInterface {
        return new OnGetProductNumberOfOrdersEvent($event->builder(), $event->product());
    }
    
    /**
     * @param float $numberOfOrders
     *
     * @return NumberOfOrders
     */
    protected function numberOfOrders(float $numberOfOrders) : NumberOfOrders
    {
        return new NumberOfOrders($numberOfOrders);
    }
    
}