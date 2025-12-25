<?php
/*--------------------------------------------------------------
   ShippingMethods.php 2022-01-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\ShippingModule\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\ShippingModule\Model\ShippingMethod;
use IteratorAggregate;
use Traversable;

/**
 * Class ShippingMethods
 *
 * @package Gambio\Admin\Modules\ShippingModule\Model\Collections
 */
class ShippingMethods implements IteratorAggregate
{
    /**
     * @var ShippingMethod[]
     */
    private array $shippingMethods;
    
    
    /**
     * ShippingMethods constructor.
     *
     * @param ShippingMethod[] $shippingMethods
     */
    private function __construct(array $shippingMethods)
    {
        $this->shippingMethods = $shippingMethods;
    }
    
    
    /**
     * @param ShippingMethod ...$shippingMethod
     *
     * @return ShippingMethods
     */
    public static function create(ShippingMethod ...$shippingMethod): ShippingMethods
    {
        return new self($shippingMethod);
    }
    
    
    /**
     * @return Traversable|ShippingMethod[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->shippingMethods);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_map(fn(ShippingMethod $method): array => $method->toArray(), $this->shippingMethods);
    }
}