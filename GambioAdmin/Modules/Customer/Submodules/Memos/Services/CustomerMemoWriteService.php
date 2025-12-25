<?php
/*--------------------------------------------------------------
   CustomerMemoWriteService.php 2021-12-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Memos\Services;

use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\Collections\CustomerMemoIds;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\CustomerMemo;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\ValueObjects\CustomerMemoId;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\Exceptions\CreationOfCustomerMemoFailedException;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\Exceptions\DeletionOfCustomerMemoFailedException;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\Exceptions\StorageOfCustomerMemoFailedException;

/**
 * Interface CustomerMemoWriteService
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Memos\Services
 */
interface CustomerMemoWriteService
{
    /**
     * Creates a new customer memo and returns its ID.
     *
     * @param int    $customerId
     * @param int    $creatorId
     * @param string $content
     *
     * @return CustomerMemoId
     *
     * @throws CreationOfCustomerMemoFailedException
     */
    public function createCustomerMemo(
        int    $customerId,
        int    $creatorId,
        string $content
    ): CustomerMemoId;
    
    
    /**
     * Creates multiple customer memos and returns their IDs.
     *
     * @param array ...$creationArguments Provided array must contain arguments like they are used in the single
     *                                    creation method. Provide multiple arrays for multi creation.
     *
     * @return CustomerMemoIds
     *
     * @throws CreationOfCustomerMemoFailedException
     */
    public function createMultipleCustomerMemos(array ...$creationArguments): CustomerMemoIds;
    
    
    /**
     * Stores multiple customer memos.
     *
     * @param CustomerMemo ...$customerMemos
     *
     * @throws StorageOfCustomerMemoFailedException
     */
    public function storeCustomerMemos(CustomerMemo ...$customerMemos): void;
    
    
    /**
     * Deletes customer memos based on the given customer memo IDs.
     *
     * @param int ...$ids
     *
     * @throws DeletionOfCustomerMemoFailedException
     */
    public function deleteCustomerMemosByMemoIds(int ...$ids): void;
    
    
    /**
     * Deletes customer memos based on the given customer IDs.
     *
     * @param int ...$ids
     *
     * @throws DeletionOfCustomerMemoFailedException
     */
    public function deleteCustomerMemosByCustomerIds(int ...$ids): void;
}