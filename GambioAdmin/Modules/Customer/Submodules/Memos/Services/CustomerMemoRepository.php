<?php
/*--------------------------------------------------------------
   CustomerMemoRepository.php 2022-09-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Memos\Services;

use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\Collections\CustomerMemoIds;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\Collections\CustomerMemos;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\CustomerMemo;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\Filter\CustomerMemoFilters;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\Filter\CustomerMemoSorting;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\ValueObjects\CreatorId;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\ValueObjects\CustomerMemoId;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\Exceptions\CreationOfCustomerMemoFailedException;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\Exceptions\CustomerMemoDoesNotExistException;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\Exceptions\DeletionOfCustomerMemoFailedException;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\Exceptions\StorageOfCustomerMemoFailedException;
use Gambio\Core\Filter\Pagination;

/**
 * Interface CustomerMemoRepository
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Memos\Services
 */
interface CustomerMemoRepository
{
    /**
     * Returns a filtered and paginated collection of customer memos based on the given filter and sorting arguments.
     *
     * @param CustomerId          $customerId
     * @param CustomerMemoFilters $filters
     * @param CustomerMemoSorting $sorting
     * @param Pagination          $pagination
     *
     * @return CustomerMemos
     */
    public function filterCustomerMemos(
        CustomerId          $customerId,
        CustomerMemoFilters $filters,
        CustomerMemoSorting $sorting,
        Pagination          $pagination
    ): CustomerMemos;
    
    
    /**
     * Returns total count of customer memos based on the given filter arguments.
     *
     * @param CustomerId          $customerId
     * @param CustomerMemoFilters $filters
     *
     * @return int
     */
    public function getCustomerMemosTotalCount(CustomerId $customerId, CustomerMemoFilters $filters): int;
    
    
    /**
     * Returns all available customer memos.
     *
     * @param CustomerId $customerId
     *
     * @return CustomerMemos
     */
    public function getCustomerMemos(CustomerId $customerId): CustomerMemos;
    
    
    /**
     * Returns a specific customer memo based on the given ID.
     *
     * @param CustomerMemoId $memoId
     *
     * @return CustomerMemo
     *
     * @throws CustomerMemoDoesNotExistException
     */
    public function getCustomerMemoById(CustomerMemoId $memoId): CustomerMemo;
    
    
    /**
     * Creates a new customer memo and returns its ID.
     *
     * @param CustomerId $customerId
     * @param CreatorId  $creatorId
     * @param string     $content
     *
     * @return CustomerMemoId
     *
     * @throws CreationOfCustomerMemoFailedException
     */
    public function createCustomerMemo(
        CustomerId $customerId,
        CreatorId  $creatorId,
        string     $content
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
     * @param CustomerMemoId ...$ids
     *
     * @throws DeletionOfCustomerMemoFailedException
     */
    public function deleteCustomerMemosByMemoIds(CustomerMemoId ...$ids): void;
    
    
    /**
     * Deletes customer memos based on the given customer IDs.
     *
     * @param CustomerId ...$ids
     *
     * @throws DeletionOfCustomerMemoFailedException
     */
    public function deleteCustomerMemosByCustomerIds(CustomerId ...$ids): void;
}