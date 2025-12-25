<?php
/*--------------------------------------------------------------
   Customers.php 2022-01-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\Customer\Model\Customer;
use IteratorAggregate;
use Traversable;

/**
 * Class Customers
 *
 * @package Gambio\Admin\Modules\Customer\Model\Collections
 * @codeCoverageIgnore
 */
class Customers implements IteratorAggregate
{
    /**
     * @var Customer []
     */
    private array $customers;
    
    
    /**
     * Customer s constructor.
     *
     * @param Customer [] $customers
     */
    private function __construct(array $customers)
    {
        $this->customers = $customers;
    }
    
    
    /**
     * @param Customer ...$customers
     *
     * @return Customers
     */
    public static function create(Customer ...$customers): Customers
    {
        return new self($customers);
    }
    
    
    /**
     * @return Traversable|Customer []
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->customers);
    }
    
    
    /**
     * @param string $datetimeFormat
     *
     * @return array
     */
    public function toArray(string $datetimeFormat = 'Y-m-d H:i:s'): array
    {
        return array_map(fn(Customer $customer): array => $customer->toArray($datetimeFormat), $this->customers);
    }
}