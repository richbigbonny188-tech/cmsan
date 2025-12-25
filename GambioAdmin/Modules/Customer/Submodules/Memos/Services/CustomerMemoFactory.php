<?php
/*--------------------------------------------------------------
   CustomerMemoFactory.php 2022-09-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Memos\Services;

use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\Collections\CustomerIds;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\Collections\CustomerMemoIds;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\Collections\CustomerMemos;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\CustomerMemo;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\ValueObjects\CreatorId;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\ValueObjects\CustomerMemoId;

/**
 * Class CustomerMemoFactory
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Memos\Services
 */
class CustomerMemoFactory
{
    /**
     * Creates and returns a collection of customer memos.
     *
     * @param CustomerMemo ...$customerMemos
     *
     * @return CustomerMemos
     */
    public function createCustomerMemos(CustomerMemo ...$customerMemos): CustomerMemos
    {
        return CustomerMemos::create(...$customerMemos);
    }
    
    
    /**
     * Creates and returns a collection of customer IDs.
     *
     * @param CustomerId ...$customerIds
     *
     * @return CustomerIds
     */
    public function createCustomerIds(CustomerId ...$customerIds): CustomerIds
    {
        return CustomerIds::create(...$customerIds);
    }
    
    
    /**
     * Creates and returns a customer ID.
     *
     * @param int $customerId
     *
     * @return CustomerId
     */
    public function createCustomerId(int $customerId): CustomerId
    {
        return CustomerId::create($customerId);
    }
    
    
    /**
     * Creates and returns a creator ID.
     *
     * @param int $creatorId
     *
     * @return CreatorId
     */
    public function createCreatorId(int $creatorId): CreatorId
    {
        return CreatorId::create($creatorId);
    }
    
    
    /**
     * Creates and returns a customer memo ID.
     *
     * @param int $customerMemoId
     *
     * @return CustomerMemoId
     */
    public function createCustomerMemoId(int $customerMemoId): CustomerMemoId
    {
        return CustomerMemoId::create($customerMemoId);
    }
    
    
    /**
     * Creates and returns a collection of customer memo IDs.
     *
     * @param CustomerMemoId ...$customerMemoIds
     *
     * @return CustomerMemoIds
     */
    public function createCustomerMemoIds(CustomerMemoId ...$customerMemoIds): CustomerMemoIds
    {
        return CustomerMemoIds::create(...$customerMemoIds);
    }
}