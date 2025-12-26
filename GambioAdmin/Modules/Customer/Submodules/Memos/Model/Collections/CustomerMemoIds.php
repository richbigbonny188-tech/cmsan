<?php
/*--------------------------------------------------------------
   CustomerMemoIds.php 2022-09-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Memos\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\ValueObjects\CustomerMemoId;
use IteratorAggregate;
use Traversable;

/**
 * Class CustomerMemoIds
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Memos\Model\Collections
 * @codeCoverageIgnore
 */
class CustomerMemoIds implements IteratorAggregate
{
    /**
     * @var CustomerMemoId[]
     */
    private array $customerMemoIds;
    
    
    /**
     * CustomerMemoIds constructor.
     *
     * @param CustomerMemoId[] $customerMemoIds
     */
    private function __construct(array $customerMemoIds)
    {
        $this->customerMemoIds = $customerMemoIds;
    }
    
    
    /**
     * @param CustomerMemoId ...$customerMemoId
     *
     * @return CustomerMemoIds
     */
    public static function create(CustomerMemoId ...$customerMemoId): CustomerMemoIds
    {
        return new self($customerMemoId);
    }
    
    
    /**
     * @return Traversable|CustomerMemoId[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->customerMemoIds);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_map(fn(CustomerMemoId $id): int => $id->value(), $this->customerMemoIds);
    }
}