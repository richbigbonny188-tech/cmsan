<?php
/* --------------------------------------------------------------
   CustomerAddonValues.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\CustomerAddonValue;
use IteratorAggregate;
use Traversable;

/**
 * Class CustomerAddonValues
 *
 * @package Gambio\Admin\Modules\CustomerAddonValue\Model\Collections
 */
class CustomerAddonValues implements IteratorAggregate
{
    /** @var CustomerAddonValue[] */
    private array $items;
    
    
    /**
     * @param CustomerAddonValue[] $items
     */
    private function __construct(array $items)
    {
        $this->items = $items;
    }
    
    
    /**
     * @param CustomerAddonValue ...$items
     *
     * @return CustomerAddonValues
     */
    public static function create(CustomerAddonValue ...$items): CustomerAddonValues
    {
        return new self($items);
    }
    
    
    /**
     * @return Traversable|CustomerAddonValue[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_map(fn(CustomerAddonValue $value): array => $value->toArray(), $this->items);
    }
}