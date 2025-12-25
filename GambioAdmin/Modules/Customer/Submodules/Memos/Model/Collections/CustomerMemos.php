<?php
/*--------------------------------------------------------------
   Customers.php 2022-09-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Memos\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\CustomerMemo;
use IteratorAggregate;
use Traversable;

/**
 * Class Customers
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Memos\Model\Collections
 * @codeCoverageIgnore
 */
class CustomerMemos implements IteratorAggregate
{
    /**
     * @var CustomerMemo []
     */
    private array $customerMemos;
    
    
    /**
     * CustomerMemo s constructor.
     *
     * @param CustomerMemo [] $customerMemos
     */
    private function __construct(array $customerMemos)
    {
        $this->customerMemos = $customerMemos;
    }
    
    
    /**
     * @param CustomerMemo ...$customerMemos
     *
     * @return CustomerMemos
     */
    public static function create(CustomerMemo ...$customerMemos): CustomerMemos
    {
        return new self($customerMemos);
    }
    
    
    /**
     * @return Traversable|CustomerMemo []
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->customerMemos);
    }
    
    
    /**
     * @param string $datetimeFormat
     *
     * @return array
     */
    public function toArray(string $datetimeFormat = 'Y-m-d H:i:s'): array
    {
        return array_map(fn(CustomerMemo $memo): array => $memo->toArray($datetimeFormat), $this->customerMemos);
    }
}