<?php
/*--------------------------------------------------------------------------------------------------
    OnGetSellingUnitStockInfoEventListener.php 2020-08-04
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Stock\SellingUnitStock\Database\Listeners;
use Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces\OnGetSellingUnitStockInfoEventInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\Interfaces\QuantityInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SellingUnitId;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SellingUnitStockInterface;
use Gambio\Shop\Stock\ValueObject\SellingUnitStock;
use ProductDataInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

class OnGetSellingUnitStockInfoEventListener
{

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * OnGetSellingUnitStockInfoEventListener constructor.
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param OnGetSellingUnitStockInfoEventInterface $event
     * @return OnGetSellingUnitStockInfoEventInterface
     */
    public function __invoke(OnGetSellingUnitStockInfoEventInterface $event): OnGetSellingUnitStockInfoEventInterface
    {
        $event->setStock($this->createStock($event->id(), $event->product(), $event->requestedQuantity()));

        return $event;
    }
    
    
    /**
     * @param SellingUnitId        $id
     * @param ProductDataInterface $data
     * @param QuantityInterface    $requestedQuantity
     *
     * @return SellingUnitStockInterface
     */
    protected function createStock(SellingUnitId $id, ProductDataInterface $data, QuantityInterface $requestedQuantity) : SellingUnitStockInterface
    {
        return new SellingUnitStock($id, $data, $requestedQuantity, $this->dispatcher);
    }
}