<?php
/*--------------------------------------------------------------
   CustomerIds.php 2022-03-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Newsletter\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\Newsletter\Model\ValueObjects\CustomerId;
use IteratorAggregate;
use Traversable;

/**
 * Class CustomerIds
 *
 * @package Gambio\Admin\Modules\Newsletter\Model\Collections
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
     * @return int[]
     */
    public function toArray(): array
    {
        return array_map(fn(CustomerId $customerIds) => $customerIds->value(), $this->customerIds);
    }
}