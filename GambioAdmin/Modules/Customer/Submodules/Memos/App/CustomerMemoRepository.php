<?php
/*--------------------------------------------------------------
   CustomerMemoRepository.php 2022-09-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

namespace Gambio\Admin\Modules\Customer\Submodules\Memos\App;

use Exception;
use Gambio\Admin\Modules\Customer\Submodules\Memos\App\Data\CustomerMemoMapper;
use Gambio\Admin\Modules\Customer\Submodules\Memos\App\Data\CustomerMemoReader;
use Gambio\Admin\Modules\Customer\Submodules\Memos\App\Data\CustomerMemoWriter;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\Collections\CustomerMemoIds;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\Collections\CustomerMemos;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\CustomerMemo;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\Events\CustomerMemoCreated;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\Events\CustomerMemoDeleted;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\Filter\CustomerMemoFilters;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\Filter\CustomerMemoSorting;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\ValueObjects\CreatorId;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\ValueObjects\CustomerMemoId;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\CustomerMemoRepository as CustomerMemoRepositoryInterface;
use Gambio\Core\Event\Abstracts\AbstractEventDispatchingRepository;
use Gambio\Core\Filter\Pagination;
use Psr\EventDispatcher\EventDispatcherInterface;
use Webmozart\Assert\Assert;

/**
 * Class CustomerMemoRepository
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Memos\App
 */
class CustomerMemoRepository extends AbstractEventDispatchingRepository implements CustomerMemoRepositoryInterface
{
    private CustomerMemoReader $reader;
    private CustomerMemoMapper $mapper;
    private CustomerMemoWriter $writer;
    
    
    /**
     * @param CustomerMemoReader       $reader
     * @param CustomerMemoWriter       $writer
     * @param CustomerMemoMapper       $mapper
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        CustomerMemoReader       $reader,
        CustomerMemoWriter       $writer,
        CustomerMemoMapper       $mapper,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->reader = $reader;
        $this->writer = $writer;
        $this->mapper = $mapper;
        $this->setEventDispatcher($eventDispatcher);
    }
    
    
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function filterCustomerMemos(
        CustomerId          $customerId,
        CustomerMemoFilters $filters,
        CustomerMemoSorting $sorting,
        Pagination          $pagination
    ): CustomerMemos {
        
        return $this->mapper->mapCustomerMemos(...$this->reader->getFilteredCustomerMemos($customerId,
                                                                                          $filters,
                                                                                          $sorting,
                                                                                          $pagination));
    }
    
    
    /**
     * @inheritDoc
     */
    public function getCustomerMemosTotalCount(CustomerId $customerId, CustomerMemoFilters $filters): int
    {
        return $this->reader->getCustomerMemosTotalCount($customerId, $filters);
    }
    
    
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getCustomerMemos(CustomerId $customerId): CustomerMemos
    {
        return $this->mapper->mapCustomerMemos(...$this->reader->getCustomerMemos($customerId));
    }
    
    
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getCustomerMemoById(CustomerMemoId $memoId): CustomerMemo
    {
        return $this->mapper->mapCustomerMemo($this->reader->getCustomerMemoById($memoId));
    }
    
    
    /**
     * @inheritDoc
     */
    public function createCustomerMemo(CustomerId $customerId, CreatorId $creatorId, string $content): CustomerMemoId
    {
        CustomerMemo::validateContent($content);
        $customerMemoId = $this->writer->createCustomerMemo($customerId, $creatorId, $content);
        $customerMemoId = $this->mapper->createCustomerMemoId($customerMemoId);
        
        $this->dispatchEvent(CustomerMemoCreated::create($customerMemoId));
        
        return $customerMemoId;
    }
    
    
    /**
     * @inheritDoc
     */
    public function createMultipleCustomerMemos(array ...$creationArguments): CustomerMemoIds
    {
        foreach ($creationArguments as $index => $args) {
            
            $this->assertArgumentIsOfCorrectType($args[0], 'First', CustomerId::class, $index);
            $this->assertArgumentIsOfCorrectType($args[1], 'Second', CreatorId::class, $index);
            $this->assertArgumentIsOfCorrectType($args[2], 'Third', 'string', $index);
            CustomerMemo::validateContent($args[2]);
        }
        
        $customerMemoIds = $this->writer->createMultipleCustomerMemos(...$creationArguments);
        $customerMemoIds = array_map([$this->mapper, 'createCustomerMemoId'], $customerMemoIds);
        $events          = array_map([CustomerMemoCreated::class, 'create'], $customerMemoIds);
        
        array_walk($events, [$this, 'dispatchEvent']);
        
        return $this->mapper->createCustomerMemoIds(...$customerMemoIds);
    }
    
    
    /**
     * @inheritDoc
     */
    public function storeCustomerMemos(CustomerMemo ...$customerMemos): void
    {
        $this->writer->storeCustomerMemos(...$customerMemos);
        array_map([$this, 'dispatchEntityEvents'], $customerMemos);
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteCustomerMemosByMemoIds(CustomerMemoId ...$ids): void
    {
        $this->writer->deleteCustomerMemosByMemoIds(...$ids);
        $events = array_map([CustomerMemoDeleted::class, 'create'], $ids);
        
        array_walk($events, [$this, 'dispatchEvent']);
    }
    
    
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function deleteCustomerMemosByCustomerIds(CustomerId ...$ids): void
    {
        $memoIds = array_merge(...array_map(function (CustomerId $id): array {
            
            return array_column($this->getCustomerMemos($id)->toArray(), 'id');
        }, $ids));
        
        $this->writer->deleteCustomerMemosByCustomerIds(...$ids);
        
        $memoIds = array_map([$this->mapper, 'createCustomerMemoId'], $memoIds);
        $events  = array_map([CustomerMemoDeleted::class, 'create'], $memoIds);
        
        array_walk($events, [$this, 'dispatchEvent']);
    }
    
    
    /**
     * @param mixed  $actual
     * @param string $arrayPosition
     * @param string $class
     * @param int    $index
     *
     * @return void
     */
    private function assertArgumentIsOfCorrectType(
        $actual,
        string $arrayPosition,
        string $class,
        int $index
    ): void {
        
        if ($class === 'string') {
            
            $message = '%s argument must be a string value';
            $message = sprintf($message, $arrayPosition);
            
            Assert::string($actual, $message);
            
            return;
        }
        
        $message = '%s argument must be instance of %s. Index: %s';
        $message = sprintf($message, $arrayPosition, $class, $index);
        
        Assert::isInstanceOf($actual, $class, $message);
    }
}