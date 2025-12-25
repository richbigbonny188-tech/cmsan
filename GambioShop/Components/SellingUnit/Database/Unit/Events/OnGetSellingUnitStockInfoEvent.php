<?php
/*--------------------------------------------------------------------------------------------------
    OnGetSellingUnitStockInfoEvent.php 2020-07-08
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Database\Unit\Events;


use Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces\OnGetSellingUnitStockInfoEventInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\Interfaces\QuantityInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SellingUnitId;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SellingUnitStockInterface;
use ProductDataInterface;

class OnGetSellingUnitStockInfoEvent implements OnGetSellingUnitStockInfoEventInterface
{
    /**
     * @var SellingUnitId
     */
    protected $id;
    /**
     * @var ProductDataInterface
     */
    protected $product;
    /**
     * @var SellingUnitStockInterface
     */
    protected $stock;
    /**
     * @var QuantityInterface
     */
    protected $requestedQuantity;

    /**
     * @return SellingUnitId
     */
    public function id(): SellingUnitId
    {
        return $this->id;
    }

    /**
     * @return ProductDataInterface
     */
    public function product(): ProductDataInterface
    {
        return $this->product;
    }

    /**
     * OnGetSellingUnitStockInfoEvent constructor.
     * @param SellingUnitId $id
     * @param ProductDataInterface $product
     * @param QuantityInterface $requestedQuantity
     */
    public function __construct(SellingUnitId $id, ProductDataInterface $product, QuantityInterface $requestedQuantity)
    {

        $this->id = $id;
        $this->product = $product;
        $this->requestedQuantity = $requestedQuantity;
    }

    /**
     * @return QuantityInterface
     */
    public function requestedQuantity(): QuantityInterface
    {
        return $this->requestedQuantity;
    }

    /**
     * @inheritDoc
     */
    public function stock(): SellingUnitStockInterface
    {
        return $this->stock;
    }

    /**
     * @inheritDoc
     */
    public function setStock(SellingUnitStockInterface $stock): void
    {
        $this->stock = $stock;
    }
}