<?php
/* --------------------------------------------------------------
 ProductVariants.php 2023-06-27
 Gambio GmbH
 http://www.gambio.de

 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Variant\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ProductVariant;
use IteratorAggregate;
use Traversable;

/**
 * Class ProductVariants
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\Model\Collections
 */
class ProductVariants implements IteratorAggregate
{
    /**
     * ProductVariants constructor.
     *
     * @param ProductVariant[] $variants
     */
    private function __construct(private array $variants) { }
    
    
    /**
     * @param ProductVariant ...$variants
     *
     * @return ProductVariants
     */
    public static function create(ProductVariant ...$variants): ProductVariants
    {
        return new self($variants);
    }
    
    
    /**
     * @return Traversable|ProductVariant[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->variants);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_map(static function (ProductVariant $variant): array {
            return $variant->toArray();
        },
            $this->variants);
    }
}
