<?php
/* --------------------------------------------------------------
   WithdrawalService.php 2020-12-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Withdrawal\App;

use Gambio\Admin\Modules\Withdrawal\Model\Collections\WithdrawalIds;
use Gambio\Admin\Modules\Withdrawal\Model\ValueObjects\CustomerDetails;
use Gambio\Admin\Modules\Withdrawal\Model\ValueObjects\OrderDetails;
use Gambio\Admin\Modules\Withdrawal\Model\ValueObjects\WithdrawalId;
use Gambio\Admin\Modules\Withdrawal\Model\Withdrawal;
use Gambio\Admin\Modules\Withdrawal\Services\WithdrawalFactory;
use Gambio\Admin\Modules\Withdrawal\Services\WithdrawalRepository;
use Gambio\Admin\Modules\Withdrawal\Services\WithdrawalWriteService as WithdrawalWriteServiceInterface;

/**
 * Class WithdrawalService
 *
 * @package Gambio\Admin\Modules\Withdrawal
 */
class WithdrawalWriteService implements WithdrawalWriteServiceInterface
{
    /**
     * @var WithdrawalRepository
     */
    private $repository;
    
    /**
     * @var WithdrawalFactory
     */
    private $factory;
    
    
    /**
     * WithdrawalWriteService constructor.
     *
     * @param WithdrawalRepository $repository
     * @param WithdrawalFactory    $factory
     */
    public function __construct(WithdrawalRepository $repository, WithdrawalFactory $factory)
    {
        $this->repository = $repository;
        $this->factory    = $factory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function createWithdrawal(
        OrderDetails $order,
        CustomerDetails $customer,
        string $date = '',
        string $content = '',
        bool $createdByAdmin = true
    ): WithdrawalId {
        return $this->repository->createWithdrawal($order, $customer, $date, $content, $createdByAdmin);
    }
    
    
    /**
     * @inheritDoc
     */
    public function createMultipleWithdrawals(array ...$creationArguments): WithdrawalIds
    {
        return $this->repository->createMultipleWithdrawals(...$creationArguments);
    }
    
    
    /**
     * @inheritDoc
     */
    public function storeWithdrawals(Withdrawal ...$withdrawals): void
    {
        $this->repository->storeWithdrawals(...$withdrawals);
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteWithdrawals(int ...$ids): void
    {
        $ids = array_map([$this->factory, 'createWithdrawalId'], $ids);
        
        $this->repository->deleteWithdrawals(... $ids);
    }
}