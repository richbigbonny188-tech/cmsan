<?php
/*--------------------------------------------------------------
   ProductVariantIds.php 2023-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Variant\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductVariantId;
use IteratorAggregate;
use Traversable;

/**
 * Class ProductVariantIds
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\Model\Collections
 */
class ProductVariantIds implements IteratorAggregate
{
    /**
     * ProductVariantIds constructor.
     *
     * @param ProductVariantId[] $ids
     */
    private function __construct(private array $ids) { }
    
    
    /**
     * @param ProductVariantId ...$ids
     *
     * @return static
     */
    public static function create(ProductVariantId ...$ids): static
    {
        return new static($ids);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_map(static function (ProductVariantId $id): int {
            return $id->value();
        },
            $this->ids);
    }
    
    
    /**
     * @return Traversable|ProductVariantId[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->ids);
    }
}