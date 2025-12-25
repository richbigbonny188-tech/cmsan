<?php
/* --------------------------------------------------------------
  CustomerMemoWriteService.php 2022-09-14
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2022 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Memos\App;

use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\Collections\CustomerMemoIds;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\CustomerMemo;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\ValueObjects\CustomerMemoId;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\CustomerMemoFactory;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\CustomerMemoRepository;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\CustomerMemoWriteService as CustomerMemoWriteServiceInterface;

/**
 * Class CustomerMemoWriteService
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Memos\App
 */
class CustomerMemoWriteService implements CustomerMemoWriteServiceInterface
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
    public function createCustomerMemo(int $customerId, int $creatorId, string $content): CustomerMemoId
    {
        return $this->repository->createCustomerMemo($this->factory->createCustomerId($customerId),
                                                     $this->factory->createCreatorId($creatorId),
                                                     $content);
    }
    
    
    /**
     * @inheritDoc
     */
    public function createMultipleCustomerMemos(array ...$creationArguments): CustomerMemoIds
    {
        $creationArguments = $this->parseCreationArguments(...$creationArguments);
        
        return $this->repository->createMultipleCustomerMemos(...$creationArguments);
    }
    
    
    /**
     * @inheritDoc
     */
    public function storeCustomerMemos(CustomerMemo ...$customerMemos): void
    {
        $this->repository->storeCustomerMemos(...$customerMemos);
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteCustomerMemosByMemoIds(int ...$ids): void
    {
        $this->repository->deleteCustomerMemosByMemoIds(...array_map([$this->factory, 'createCustomerMemoId'], $ids));
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteCustomerMemosByCustomerIds(int ...$ids): void
    {
        $this->repository->deleteCustomerMemosByCustomerIds(...array_map([$this->factory, 'createCustomerId'], $ids));
    }
    
    
    /**
     * @param array ...$creationArguments
     *
     * @return array
     */
    private function parseCreationArguments(array ...$creationArguments): array
    {
        $result = [];
        
        foreach ($creationArguments as $args) {
            
            $result[] = [
                $this->factory->createCustomerId($args[0]),
                $this->factory->createCreatorId($args[1]),
                $args[2],
            ];
        }
        
        return $result;
    }
}