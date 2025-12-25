<?php
/*--------------------------------------------------------------
   DeleteAddressesAfterDeletionOfACustomerEventListener.php 2022-10-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Address\App\EventListeners;

use Exception;
use Gambio\Admin\Modules\Customer\Model\Events\CustomerDeleted;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\CustomerAddressFactory;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\CustomerAddressRepository;
use function Gambio\Core\Logging\logger;

/**
 * Class DeleteAddressesAfterDeletionOfACustomerEventListener
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Address\App\EventListeners
 */
class DeleteAddressesAfterDeletionOfACustomerEventListener
{
    private CustomerAddressRepository $repository;
    private CustomerAddressFactory    $factory;
    
    
    /**
     * @param CustomerAddressRepository $repository
     * @param CustomerAddressFactory    $factory
     */
    public function __construct(
        CustomerAddressRepository $repository,
        CustomerAddressFactory    $factory
    ) {
        $this->repository = $repository;
        $this->factory    = $factory;
    }
    
    
    /**
     * @param CustomerDeleted $event
     *
     * @return void
     */
    public function __invoke(CustomerDeleted $event): void
    {
        $id = $this->factory->createCustomerId($customerIdInt = $event->customerId()->value());
        
        try {
            $addresses  = $this->repository->getAddresses($id)->toArray();
            $addressIds = array_map(fn(array $address): int => $address['id'], $addresses);
            $addressIds = array_map([$this->factory, 'createCustomerAddressId'], $addressIds);
            
            array_walk($addressIds, [$this->repository, 'deleteCustomerAddress']);
        } catch (Exception $exception) {
            // @codeCoverageIgnoreStart
            $message = 'Could not delete addresses for customer with ID %s because a %s was thrown';
            logger()->error(sprintf($message, $customerIdInt, get_class($exception)));
            // @codeCoverageIgnoreEnd
        }
    }
}