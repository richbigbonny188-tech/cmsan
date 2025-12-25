<?php
/*--------------------------------------------------------------------------------------------------
    OnCreateSellingUnitListener.php 2020-02-25
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */
namespace Gambio\Shop\SellingUnit\Database\Unit\Listener;

use Gambio\Shop\SellingUnit\Database\Image\Events\OnImageCollectionCreateEventInterface;
use Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces\OnCreateSellingUnitEventInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SelectedQuantity;
use Psr\EventDispatcher\EventDispatcherInterface;

class OnCreateSellingUnitListener
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;
    
    
    /**
     * OnCreateSellingUnitListener constructor.
     *
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
    
        $this->dispatcher = $dispatcher;
    }
    
    
    /**
     * @param OnCreateSellingUnitEventInterface $event
     *
     * @return OnCreateSellingUnitEventInterface
     */
    public function __invoke(OnCreateSellingUnitEventInterface $event): OnCreateSellingUnitEventInterface
    {
        $event->builder()->withId($event->id())
                         ->withDispatcher($this->dispatcher)
                         ->withProduct($event->product())
                         ->withXtcPrice($event->xtcPrice());
        return $event;
    }
    
}