<?php
/*--------------------------------------------------------------
   ProductIds.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Orders\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\Customer\Submodules\Orders\Model\ValueObjects\ProductId;
use IteratorAggregate;
use Traversable;

/**
 * Class ProductIds
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Orders\Model\Collections
 */
class ProductIds implements IteratorAggregate
{
    /**
     * @var ProductId[]
     */
    private array $productIds;
    
    
    /**
     * ProductIds constructor.
     *
     * @param ProductId[] $productIds
     */
    private function __construct(array $productIds)
    {
        $this->productIds = $productIds;
    }
    
    
    /**
     * @param ProductId ...$productIds
     *
     * @return ProductIds
     */
    public static function create(ProductId ...$productIds): ProductIds
    {
        return new self($productIds);
    }
    
    
    /**
     * @return Traversable|ProductId[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->productIds);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_map(fn(ProductId $id): int => $id->value(), $this->productIds);
    }
}