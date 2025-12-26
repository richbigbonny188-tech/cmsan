<?php
/*--------------------------------------------------------------
   CustomerIds.php 2022-01-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
 
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\CustomerId;
use IteratorAggregate;
use Traversable;

/**
 * Class CustomerIds
 *
 * @package Gambio\Admin\Modules\Customer\Model\Collections
 * @codeCoverageIgnore
 */
class CustomerIds implements IteratorAggregate
{
    /**
     * @var CustomerId[]
     */
    private array $customerIds;
    
    
    /**
     * CustomerIds constructor.
     *
     * @param CustomerId[] $customerIds
     */
    private function __construct(array $customerIds)
    {
        $this->customerIds = $customerIds;
    }
    
    
    /**
     * @param CustomerId ...$customerIds
     *
     * @return CustomerIds
     */
    public static function create(CustomerId ...$customerIds): CustomerIds
    {
        return new self($customerIds);
    }
    
    
    /**
     * @return Traversable|CustomerId[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->customerIds);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_map(fn(CustomerId $id): int => $id->value(), $this->customerIds);
    }
}