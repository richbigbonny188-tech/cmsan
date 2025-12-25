<?php
/*--------------------------------------------------------------
   SoldProducts.php 2023-09-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Model\SoldProduct;
use IteratorAggregate;
use Traversable;

/**
 * Class SoldProducts
 *
 * @package Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Model\Collections
 */
class SoldProducts implements IteratorAggregate
{
    /**
     * SoldProducts constructor.
     *
     * @param array $soldProducts
     */
    private function __construct(private array $soldProducts) { }
    
    
    /**
     * @param SoldProduct ...$soldProducts
     *
     * @return SoldProducts
     */
    public static function create(SoldProduct ...$soldProducts): SoldProducts
    {
        return new self($soldProducts);
    }
    
    
    /**
     * @return Traversable|SoldProduct[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->soldProducts);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_map(fn(SoldProduct $product): array => $product->toArray(), $this->soldProducts);
    }
}