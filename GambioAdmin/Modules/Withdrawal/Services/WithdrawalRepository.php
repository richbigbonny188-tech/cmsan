<?php
/* --------------------------------------------------------------
   WithdrawalRepository.php 2021-04-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Withdrawal\Services;

use Gambio\Admin\Modules\Withdrawal\Model\Collections\WithdrawalIds;
use Gambio\Admin\Modules\Withdrawal\Model\Collections\Withdrawals;
use Gambio\Admin\Modules\Withdrawal\Model\ValueObjects\CustomerDetails;
use Gambio\Admin\Modules\Withdrawal\Model\ValueObjects\OrderDetails;
use Gambio\Admin\Modules\Withdrawal\Model\ValueObjects\WithdrawalId;
use Gambio\Admin\Modules\Withdrawal\Model\Withdrawal;
use Gambio\Admin\Modules\Withdrawal\Services\Exceptions\CreationOfWithdrawalFailedException;
use Gambio\Admin\Modules\Withdrawal\Services\Exceptions\DeletionOfWithdrawalsFailedException;
use Gambio\Admin\Modules\Withdrawal\Services\Exceptions\StorageOfWithdrawalsFailedException;
use Gambio\Admin\Modules\Withdrawal\Services\Exceptions\WithdrawalNotFoundException;
use Gambio\Core\Filter\Filters;
use Gambio\Core\Filter\Pagination;
use Gambio\Core\Filter\Sorting;

/**
 * Interface WithdrawalRepository
 *
 * @package Gambio\Admin\Modules\Withdrawal\Services
 */
interface WithdrawalRepository
{
    /**
     * Returns a filtered and paginated collection of withdrawals based on the given filter and sorting arguments.
     *
     * @param Filters    $filters
     * @param Sorting    $sorting
     * @param Pagination $pagination
     *
     * @return Withdrawals
     */
    public function filterWithdrawals(Filters $filters, Sorting $sorting, Pagination $pagination): Withdrawals;
    
    
    /**
     * Returns total count of withdrawals based on the given filter arguments.
     *
     * @param Filters $criteria
     *
     * @return int
     */
    public function getWithdrawalsTotalCount(Filters $criteria): int;
    
    
    /**
     * Returns all available withdrawals.
     *
     * @return Withdrawals
     */
    public function getAllWithdrawals(): Withdrawals;
    
    
    /**
     * Returns a specific withdrawals based on the given ID.
     *
     * @param WithdrawalId $id
     *
     * @return Withdrawal
     *
     * @throws WithdrawalNotFoundException
     */
    public function getWithdrawalById(WithdrawalId $id): Withdrawal;
    
    
    /**
     * Creates a new withdrawals and returns its ID.
     *
     * @param OrderDetails    $order
     * @param CustomerDetails $customer
     * @param string|null     $date
     * @param string          $content
     * @param bool            $createdByAdmin
     *
     * @return WithdrawalId
     *
     * @throws CreationOfWithdrawalFailedException
     */
    public function createWithdrawal(
        OrderDetails $order,
        CustomerDetails $customer,
        string $date = null,
        string $content = '',
        bool $createdByAdmin = true
    ): WithdrawalId;
    
    
    /**
     * Creates multiple withdrawals and returns their IDs.
     *
     * @param array ...$creationArguments Provided array must contain arguments like they are used in the single
     *                                    creation method. Provide multiple arrays for multi creation.
     *
     * @return WithdrawalIds
     *
     * @throws CreationOfWithdrawalFailedException
     */
    public function createMultipleWithdrawals(array ...$creationArguments): WithdrawalIds;
    
    
    /**
     * Stores multiple withdrawals.
     *
     * @param Withdrawal ...$withdrawals
     *
     * @throws StorageOfWithdrawalsFailedException
     */
    public function storeWithdrawals(Withdrawal ...$withdrawals): void;
    
    
    /**
     * Deletes withdrawals based on the given IDs.
     *
     * @param WithdrawalId ...$ids
     *
     * @throws DeletionOfWithdrawalsFailedException
     */
    public function deleteWithdrawals(WithdrawalId ...$ids): void;
}