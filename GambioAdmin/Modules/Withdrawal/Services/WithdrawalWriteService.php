<?php
/* --------------------------------------------------------------
   WithdrawalWriteService.php 2021-04-07
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
use Gambio\Admin\Modules\Withdrawal\Model\ValueObjects\CustomerDetails;
use Gambio\Admin\Modules\Withdrawal\Model\ValueObjects\OrderDetails;
use Gambio\Admin\Modules\Withdrawal\Model\ValueObjects\WithdrawalId;
use Gambio\Admin\Modules\Withdrawal\Model\Withdrawal;
use Gambio\Admin\Modules\Withdrawal\Services\Exceptions\CreationOfWithdrawalFailedException;
use Gambio\Admin\Modules\Withdrawal\Services\Exceptions\DeletionOfWithdrawalsFailedException;
use Gambio\Admin\Modules\Withdrawal\Services\Exceptions\StorageOfWithdrawalsFailedException;

/**
 * Interface WithdrawalWriteService
 *
 * @package Gambio\Admin\Modules\Withdrawal\Services
 */
interface WithdrawalWriteService
{
    /**
     * Creates a new withdrawals and returns its ID.
     *
     * @param OrderDetails    $order
     * @param CustomerDetails $customer
     * @param string          $date
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
        string $date = '',
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
     * @param int ...$ids
     *
     * @throws DeletionOfWithdrawalsFailedException
     */
    public function deleteWithdrawals(int ...$ids): void;
}