<?php
/*--------------------------------------------------------------------
 OnGetProductInfoEventListener.php 2020-04-28
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Product\Name\Listener;

use Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces\OnGetProductInfoEventInterface;
use Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces\OnGetProductNameEventInterface;
use Gambio\Shop\SellingUnit\Database\Unit\Events\OnGetProductNameEvent;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\Name;

/**
 * Class OnGetProductInfoEventListener
 * @package Gambio\Shop\Product\Name\Listener
 */
class OnGetProductInfoEventListener
{
    /**
     * @param OnGetProductInfoEventInterface $event
     */
    public function __invoke(OnGetProductInfoEventInterface $event)
    {
        $product = $event->product();
        $name    = $this->name($product->getProductsName());
    
        $event->builder()->withName($name);
        $event->dispatcher()->dispatch($this->createOnGetProductNameEvent($event));
    }
    
    
    /**
     * @param OnGetProductInfoEventInterface $event
     *
     * @return OnGetProductNameEventInterface
     */
    protected function createOnGetProductNameEvent(OnGetProductInfoEventInterface $event): OnGetProductNameEventInterface
    {
        return new OnGetProductNameEvent($event->builder(), $event->product());
    }
    
    /**
     * @param string $name
     *
     * @return Name|null
     */
    protected function name(?string $name): Name
    {
        return new Name($name);
    }
}