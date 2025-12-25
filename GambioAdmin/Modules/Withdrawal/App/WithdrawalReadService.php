<?php
/* --------------------------------------------------------------
   WithdrawalReadService.php 2020-08-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Withdrawal\App;

use Gambio\Admin\Modules\Withdrawal\Model\Collections\Withdrawals;
use Gambio\Admin\Modules\Withdrawal\Model\Withdrawal;
use Gambio\Admin\Modules\Withdrawal\Services\WithdrawalFactory;
use Gambio\Admin\Modules\Withdrawal\Services\WithdrawalReadService as WithdrawalReadServiceInterface;
use Gambio\Admin\Modules\Withdrawal\Services\WithdrawalRepository;

/**
 * Class WithdrawalService
 *
 * @package Gambio\Admin\Modules\Withdrawal
 */
class WithdrawalReadService implements WithdrawalReadServiceInterface
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
     * WithdrawalReadService constructor.
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
    public function getWithdrawals(): Withdrawals
    {
        return $this->repository->getAllWithdrawals();
    }
    
    
    /**
     * @inheritDoc
     */
    public function getWithdrawalById(int $id): Withdrawal
    {
        return $this->repository->getWithdrawalById($this->factory->createWithdrawalId($id));
    }
}