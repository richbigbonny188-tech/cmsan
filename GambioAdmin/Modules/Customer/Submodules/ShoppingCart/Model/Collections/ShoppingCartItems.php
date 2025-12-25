<?php
/*--------------------------------------------------------------
   ShoppingCartItems.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Model\ValueObjects\ShoppingCartItem;
use IteratorAggregate;

class ShoppingCartItems implements IteratorAggregate
{
    /**
     * @var ShoppingCartItem[] $values
     */
    private array $values;
    
    
    /**
     * Constructor
     */
    private function __construct(array $values)
    {
        $this->values = $values;
    }
    
    
    /**
     * Create a new instance of ShoppingCartItems
     */
    public static function create(ShoppingCartItem ...$items): self
    {
        return new self($items);
    }
    
    
    /**
     * returns the Shopping Cart Items
     */
    public function toArray(string $format = "Y-m-d"): array
    {
        return array_map(fn($value): array => $value->toArray($format), $this->values);
    }
    
    
    /**
     * @return ArrayIterator|ShoppingCartItem[]
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->values);
    }
}