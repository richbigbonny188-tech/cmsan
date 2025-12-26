<?php
/*--------------------------------------------------------------
   CustomerAddressIds.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Address\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects\CustomerAddressId;
use IteratorAggregate;
use Traversable;

/**
 * Class CustomerAddressIds
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Address\Collections
 */
class CustomerAddressIds implements IteratorAggregate
{
    /**
     * @var CustomerAddressId[]
     */
    private array $customerAddressIds;
    
    
    /**
     * CustomerAddressIds constructor.
     *
     * @param CustomerAddressId[] $customerAddressIds
     */
    private function __construct(array $customerAddressIds)
    {
        $this->customerAddressIds = $customerAddressIds;
    }
    
    
    /**
     * @param CustomerAddressId ...$customerAddressIds
     *
     * @return CustomerAddressIds
     */
    public static function create(CustomerAddressId ...$customerAddressIds): CustomerAddressIds
    {
        return new self($customerAddressIds);
    }
    
    
    /**
     * @return Traversable|CustomerAddressId[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->customerAddressIds);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_map(fn(CustomerAddressId $id): int => $id->value(), $this->customerAddressIds);
    }
}