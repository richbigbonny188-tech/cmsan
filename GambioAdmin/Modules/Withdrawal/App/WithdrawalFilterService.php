<?php
/* --------------------------------------------------------------
   WithdrawalService.php 2020-10-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Withdrawal\App;

use Gambio\Admin\Modules\Withdrawal\App\Data\Filter\WithdrawalFilterFactory;
use Gambio\Admin\Modules\Withdrawal\Model\Collections\Withdrawals;
use Gambio\Admin\Modules\Withdrawal\Services\WithdrawalFilterService as WithdrawalFilterServiceInterface;
use Gambio\Admin\Modules\Withdrawal\Services\WithdrawalRepository;

/**
 * Class WithdrawalService
 *
 * @package Gambio\Admin\Modules\Withdrawal
 */
class WithdrawalFilterService implements WithdrawalFilterServiceInterface
{
    /**
     * @var WithdrawalRepository
     */
    private $repository;
    
    /**
     * @var WithdrawalFilterFactory
     */
    private $factory;
    
    
    /**
     * WithdrawalFilterService constructor.
     *
     * @param WithdrawalRepository    $repository
     * @param WithdrawalFilterFactory $factory
     */
    public function __construct(WithdrawalRepository $repository, WithdrawalFilterFactory $factory)
    {
        $this->repository = $repository;
        $this->factory    = $factory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function filterWithdrawals(
        array $filters,
        ?string $sorting = null,
        int $limit = 25,
        int $offset = 0
    ): Withdrawals {
        return $this->repository->filterWithdrawals($this->factory->createFilters($filters),
                                                    $this->factory->createSorting($sorting),
                                                    $this->factory->createPagination($limit, $offset));
    }
    
    
    /**
     * @inheritDoc
     */
    public function getWithdrawalsTotalCount(array $filters): int
    {
        return $this->repository->getWithdrawalsTotalCount($this->factory->createFilters($filters));
    }
}