<?php
/*--------------------------------------------------------------
   CustomerHistoryReadService.php 2022-01-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\History\App;

use Gambio\Admin\Modules\Customer\Submodules\History\Model\Collections\CustomerHistory;
use Gambio\Admin\Modules\Customer\Submodules\History\Services\CustomerHistoryFactory;
use Gambio\Admin\Modules\Customer\Submodules\History\Services\CustomerHistoryReadService as CustomerHistoryReadServiceInterface;
use Gambio\Admin\Modules\Customer\Submodules\History\Services\CustomerHistoryRepository;

/**
 * Class CustomerHistoryReadService
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\History\App
 */
class CustomerHistoryReadService implements CustomerHistoryReadServiceInterface
{
    private CustomerHistoryRepository $repository;
    private CustomerHistoryFactory    $factory;
    
    
    /**
     * @param \Gambio\Admin\Modules\Customer\Submodules\History\App\CustomerHistoryRepository $repository
     * @param CustomerHistoryFactory                                                          $factory
     */
    public function __construct(
        CustomerHistoryRepository $repository,
        CustomerHistoryFactory    $factory
    ) {
        $this->repository = $repository;
        $this->factory    = $factory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getCustomerHistory(int $customerId): CustomerHistory
    {
        return $this->repository->getCustomerHistory($this->factory->createCustomerId($customerId));
    }
    
    
    /**
     * @inheritDoc
     */
    public function getCustomerHistoryForType(int $customerId, string $type): CustomerHistory
    {
        return $this->repository->getCustomerHistoryForType($this->factory->createCustomerId($customerId), $type);
    }
}