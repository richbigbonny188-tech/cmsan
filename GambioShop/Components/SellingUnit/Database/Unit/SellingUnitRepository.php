<?php
/*--------------------------------------------------------------------------------------------------
    SellingUnitRepository.php 2020-08-07
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */


namespace Gambio\Shop\SellingUnit\Database\Unit;

use Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces\OnCreateSellingUnitEventInterface;
use Gambio\Shop\SellingUnit\Database\Unit\Events\OnCreateSellingUnitEvent;
use Gambio\Shop\SellingUnit\Unit\SellingUnitInterface;
use Gambio\Shop\SellingUnit\Unit\SellingUnitRepositoryInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\Interfaces\QuantityInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SelectedQuantity;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SellingUnitId;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class SellingUnitRepository
 */
class SellingUnitRepository implements SellingUnitRepositoryInterface
{
    
    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;
    
    
    /**
     * SellingUnitRepository constructor.
     *
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        
        $this->dispatcher = $dispatcher;
    }
    
    /**
     * @inheritDoc
     */
    public function getSellingUnitBy(SellingUnitId $id, $product = null, $xtcPrice = null, QuantityInterface $quantity = null): SellingUnitInterface
    {
        /**
         * @var OnCreateSellingUnitEventInterface $event
         */
        $event = new OnCreateSellingUnitEvent($id, $product, $xtcPrice, $quantity);
        $event = $this->dispatcher->dispatch($event);
        
        return $event->builder()->build();
    }
    
    
}