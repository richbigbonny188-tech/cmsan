<?php
/* --------------------------------------------------------------
  OnGetProductInfoEventListener.php 2020-02-19
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Product\Status\Listener;

use Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces\OnGetProductInfoEventInterface;
use Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces\OnGetProductStatusEventInterface;
use Gambio\Shop\SellingUnit\Database\Unit\Events\OnGetProductStatusEvent;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\ProductStatus;

/**
 * Class OnGetProductInfoEventListener
 * @package Gambio\Shop\Product\Status\Listener
 */
class OnGetProductInfoEventListener
{
    /**
     * @param OnGetProductInfoEventInterface $event
     */
    public function __invoke(OnGetProductInfoEventInterface $event)
    {
        $product      = $event->product();
        $status = $this->status($product->getStatus());
        
        $event->builder()->withStatus($status);
        $event->dispatcher()->dispatch($this->createOnGetProductLegalAgeFlagEvent($event));
    }
    
    
    protected function createOnGetProductLegalAgeFlagEvent(OnGetProductInfoEventInterface $event
    ) : OnGetProductStatusEventInterface {
        return new OnGetProductStatusEvent($event->builder(), $event->product());
    }
    
    
    /**
     * @param bool $status
     *
     * @return ProductStatus
     */
    protected function status(bool $status) : ProductStatus
    {
        return new ProductStatus($status);
    }
    
}