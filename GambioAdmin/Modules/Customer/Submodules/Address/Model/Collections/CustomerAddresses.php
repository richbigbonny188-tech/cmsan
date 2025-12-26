<?php
/*--------------------------------------------------------------
   CustomerAddresses.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Address\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\CustomerAddress;
use IteratorAggregate;
use Traversable;

/**
 * Class CustomerAddresses
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Address\Collections
 */
class CustomerAddresses implements IteratorAggregate
{
    /**
     * @var CustomerAddress[]
     */
    private array $customerAddressIds;
    
    
    /**
     * CustomerAddresses constructor.
     *
     * @param CustomerAddress[] $customerAddressIds
     */
    private function __construct(array $customerAddressIds)
    {
        $this->customerAddressIds = $customerAddressIds;
    }
    
    
    /**
     * @param CustomerAddress ...$customerAddresses
     *
     * @return CustomerAddresses
     */
    public static function create(CustomerAddress ...$customerAddresses): CustomerAddresses
    {
        return new self($customerAddresses);
    }
    
    
    /**
     * @return Traversable|CustomerAddress[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->customerAddressIds);
    }
    
    
    /**
     * @param string $datetimeFormat
     *
     * @return array
     */
    public function toArray(string $datetimeFormat = 'Y-m-d H:i:s'): array
    {
        return array_map(static function (CustomerAddress $address) use ($datetimeFormat): array {
            return $address->toArray($datetimeFormat);
        },
            $this->customerAddressIds);
    }
}