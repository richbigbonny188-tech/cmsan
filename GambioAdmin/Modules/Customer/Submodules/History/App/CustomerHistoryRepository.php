<?php
/*--------------------------------------------------------------
   CustomerHistoryRepository.php 2023-06-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\History\App;

use Gambio\Admin\Modules\Customer\Submodules\History\App\Data\CustomerHistoryMapper;
use Gambio\Admin\Modules\Customer\Submodules\History\Model\Collections\CustomerHistory;
use Gambio\Admin\Modules\Customer\Submodules\History\Model\Events\CustomerHistoryCollected;
use Gambio\Admin\Modules\Customer\Submodules\History\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Submodules\History\Services\CustomerHistoryReader;
use Gambio\Admin\Modules\Customer\Submodules\History\Services\CustomerHistoryRepository as CustomerHistoryRepositoryInterface;
use Gambio\Core\Event\Abstracts\AbstractEventDispatchingRepository;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class CustomerHistoryRepository
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\History\App
 */
class CustomerHistoryRepository extends AbstractEventDispatchingRepository implements CustomerHistoryRepositoryInterface
{
    /** @var CustomerHistoryReader[] */
    private array $readers;
    
    
    /**
     * @param CustomerHistoryMapper    $mapper
     * @param EventDispatcherInterface $dispatcher
     * @param CustomerHistoryReader    ...$readers
     */
    public function __construct(
        private CustomerHistoryMapper $mapper,
        EventDispatcherInterface      $dispatcher,
        CustomerHistoryReader         ...$readers
    ) {
        $this->setEventDispatcher($dispatcher);
        foreach ($readers as $reader) {
            $this->readers[$reader->getType()] = $reader;
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function getCustomerHistory(CustomerId $customerId): CustomerHistory
    {
        $result = $this->mapper->createCustomerHistory();
    
        foreach ($this->readers as $reader) {
            
            $dtos = $reader->getCustomerHistoryEntries($customerId);
    
            $result = $result->merge($this->mapper->mapCustomerHistory($dtos));
        }
        
        $event = new CustomerHistoryCollected($customerId, $this->mapper->createCustomerHistoryEntryDtos());
        $this->dispatchEvent($event);
        
        if (count($event->dtos())) {
            $result = $result->merge($this->mapper->mapCustomerHistory($event->dtos()));
        }
        
        return $result;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getCustomerHistoryForType(CustomerId $customerId, string $type): CustomerHistory
    {
        $result = $this->mapper->createCustomerHistory();
    
        foreach ($this->readers as $reader) {
        
            if ($reader->getType() !== $type) {
                
                continue;
            }
            
            $dtos = $reader->getCustomerHistoryEntries($customerId);
    
            $result = $result->merge($this->mapper->mapCustomerHistory($dtos));
        }
        
        $event  = new CustomerHistoryCollected($customerId, $this->mapper->createCustomerHistoryEntryDtos());
        $this->dispatchEvent($event);
        
        if (count($event->dtos()->filterByType($type))) {
            $result = $result->merge($this->mapper->mapCustomerHistory($event->dtos()->filterByType($type)));
        }
    
        return $result;
    }
    
    
    /**
     * @inheritDoc
     */
    public function registerCustomerHistoryReader(CustomerHistoryReader $reader): void
    {
        $this->readers[$reader->getType()] = $reader;
    }
}