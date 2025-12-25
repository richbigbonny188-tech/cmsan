<?php
/* --------------------------------------------------------------
  OnGetProductInfoEventListener.php 2020-06-06
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Product\Description\Listener;

use Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces\OnGetProductDescriptionEventInterface;
use Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces\OnGetProductInfoEventInterface;
use Gambio\Shop\SellingUnit\Database\Unit\Events\OnGetProductDescriptionEvent;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\Description;

/**
 * Class OnGetProductInfoEventListener
 * @package Gambio\Shop\Product\Description\Listener
 */
class OnGetProductInfoEventListener
{
    /**
     * @param OnGetProductInfoEventInterface $event
     */
    public function __invoke(OnGetProductInfoEventInterface $event)
    {
        $product     = $event->product();
        $description = $this->description((string)$product->getProductsDescription());
        
        $event->builder()->withDescription($description);
        $event->dispatcher()->dispatch($this->createOnGetProductDescriptionEvent($event));
    }
    
    
    /**
     * @param OnGetProductInfoEventInterface $event
     *
     * @return OnGetProductDescriptionEventInterface
     */
    protected function createOnGetProductDescriptionEvent(OnGetProductInfoEventInterface $event
    ) : OnGetProductDescriptionEventInterface {
        return new OnGetProductDescriptionEvent($event->builder(), $event->product());
    }
    
    
    /**
     * @param string $description
     *
     * @return Description
     */
    protected function description(string $description) : Description
    {
        return new Description($description);
    }
    
}