<?php
/*--------------------------------------------------------------------------------------------------
    SellingUnitReadService.php 2020-3-3
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2016 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\SellingUnit\Unit\Services;

use Gambio\Shop\SellingUnit\Unit\SellingUnitInterface;
use Gambio\Shop\SellingUnit\Unit\SellingUnitRepositoryInterface;
use Gambio\Shop\SellingUnit\Unit\Services\Interfaces\SellingUnitReadServiceInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\Interfaces\QuantityInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SelectedQuantity;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SellingUnitId;

/**
 * Class SellingUnitReadService
 * @package Gambio\Shop\SellingUnit\Unit\Services
 */
class SellingUnitReadService implements SellingUnitReadServiceInterface
{
    /**
     * @var SellingUnitRepositoryInterface
     */
    private $repository;
    
    
    /**
     * SellingUnitReadService constructor.
     *
     * @param SellingUnitRepositoryInterface $repository
     */
    public function __construct(SellingUnitRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    
    /**
     * @inheritDoc
     */
    public function getSellingUnitBy(SellingUnitId $id, $product = null, $xtcPrice = null, QuantityInterface $quantity = null): SellingUnitInterface
    {
        return $this->repository->getSellingUnitBy($id, $product, $xtcPrice, $quantity);
    }
}