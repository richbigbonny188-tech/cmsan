<?php
/*--------------------------------------------------------------
   CustomerMemoReadService.php 2022-09-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

namespace Gambio\Admin\Modules\Customer\Submodules\Memos\App;

use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\Collections\CustomerMemos;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\CustomerMemo;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\CustomerMemoFactory;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\CustomerMemoReadService as CustomerMemoReadServiceInterface;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\CustomerMemoRepository;

/**
 * Class CustomerMemoReadService
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Memos\App
 */
class CustomerMemoReadService implements CustomerMemoReadServiceInterface
{
    private CustomerMemoRepository $repository;
    private CustomerMemoFactory    $factory;
    
    
    /**
     * @param \Gambio\Admin\Modules\Customer\Submodules\Memos\App\CustomerMemoRepository $repository
     * @param CustomerMemoFactory                                                        $factory
     */
    public function __construct(CustomerMemoRepository $repository, CustomerMemoFactory $factory)
    {
        $this->repository = $repository;
        $this->factory    = $factory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getCustomerMemos(int $customerId): CustomerMemos
    {
        return $this->repository->getCustomerMemos($this->factory->createCustomerId($customerId));
    }
    
    
    /**
     * @inheritDoc
     */
    public function getCustomerMemoById(int $memoId): CustomerMemo
    {
        return $this->repository->getCustomerMemoById($this->factory->createCustomerMemoId($memoId));
    }
}