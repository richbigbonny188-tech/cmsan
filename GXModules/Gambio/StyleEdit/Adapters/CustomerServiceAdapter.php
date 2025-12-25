<?php
/*--------------------------------------------------------------------------------------------------
    CustomerServiceAdapter.php 2020-08-05
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace GXModules\Gambio\StyleEdit\Adapters;

use GXModules\Gambio\StyleEdit\Adapters\Interfaces\CustomerServiceAdapterInterface;
use CustomerReadServiceInterface;

/**
 * Class CustomerServiceAdapter
 * @package GXModules\Gambio\StyleEdit\Adapters
 */
class CustomerServiceAdapter implements CustomerServiceAdapterInterface
{
    /**
     * @var CustomerReadServiceInterface
     */
    protected $customerReaderService;
    
    
    /**
     * CustomerServiceAdapter constructor.
     *
     * @param CustomerReadServiceInterface $customerReaderService
     */
    public function __construct(CustomerReadServiceInterface $customerReaderService)
    {
        $this->customerReaderService = $customerReaderService;
    }
    
    
    /**
     * @param int $customerId
     *
     * @return \Customer
     */
    public function getCustomerById(int $customerId): \Customer
    {
        return $this->customerReaderService->getCustomerById(new \IdType($customerId));
    }
}