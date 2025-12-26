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

namespace Gambio\Shop\Product\AvailabilityDate\Listener;

use Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces\OnGetProductAvailabilityDateEventInterface;
use Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces\OnGetProductInfoEventInterface;
use Gambio\Shop\SellingUnit\Database\Unit\Events\OnGetProductAvailabilityDateEvent;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\AvailabilityDate;

/**
 * Class OnGetProductInfoEventListener
 * @package Gambio\Shop\Product\AvailabilityDate\Listener
 */
class OnGetProductInfoEventListener
{
    /**
     * @param OnGetProductInfoEventInterface $event
     */
    public function __invoke(OnGetProductInfoEventInterface $event)
    {
        $product     = $event->product();
        $availabilityDate = $this->availabilityDate($product->getAvailabilityDate());
        
        $event->builder()->withAvailabilityDate($availabilityDate);
        $event->dispatcher()->dispatch($this->createOnGetProductAvailabilityDateEvent($event));
    }
    
    
    /**
     * @param OnGetProductInfoEventInterface $event
     *
     * @return OnGetProductAvailabilityDateEventInterface
     */
    protected function createOnGetProductAvailabilityDateEvent(OnGetProductInfoEventInterface $event
    ) : OnGetProductAvailabilityDateEventInterface {
        return new OnGetProductAvailabilityDateEvent($event->builder(), $event->product());
    }
    
    
    /**
     * @param string $availabilityDate
     *
     * @return AvailabilityDate
     */
    protected function availabilityDate(?string $availabilityDate) : AvailabilityDate
    {
        return new AvailabilityDate($availabilityDate);
    }
    
}