<?php
/* --------------------------------------------------------------
   CustomerAddonValueIds.php 2022-09-15
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
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\ValueObjects\CustomerAddonValueId;
use IteratorAggregate;
use Traversable;

/**
 * Class CustomerAddonValueIds
 *
 * @package Gambio\Admin\Modules\CustomerAddonValue\Model\Collections
 */
class CustomerAddonValueIds implements IteratorAggregate
{
    /** @var CustomerAddonValueId[] */
    private array $items;
    
    
    /**
     * @param CustomerAddonValueId[] $items
     */
    private function __construct(array $items)
    {
        $this->items = $items;
    }
    
    
    /**
     * @param CustomerAddonValueId ...$items
     *
     * @return CustomerAddonValueIds
     */
    public static function create(CustomerAddonValueId ...$items): CustomerAddonValueIds
    {
        return new self($items);
    }
    
    
    /**
     * @return Traversable|CustomerAddonValueId[]
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
        return array_map(fn(CustomerAddonValueId $id): array => $id->toArray(), $this->items);
    }
}