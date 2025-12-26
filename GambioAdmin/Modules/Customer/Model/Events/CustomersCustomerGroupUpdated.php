<?php
/*--------------------------------------------------------------
   CustomersCustomerGroupUpdated.php 2022-01-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Model\Events;

use Gambio\Admin\Modules\Customer\Model\ValueObjects\CustomerGroup;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\CustomerId;

/**
 * Class CustomersCustomerGroupUpdated
 *
 * @package Gambio\Admin\Modules\Customer\Model\Events
 * @codeCoverageIgnore
 */
class CustomersCustomerGroupUpdated
{
    private CustomerId    $customerId;
    private CustomerGroup $customerGroup;
    
    
    /**
     * @param CustomerId    $customerId
     * @param CustomerGroup $customerGroup
     */
    private function __construct(CustomerId $customerId, CustomerGroup $customerGroup)
    {
        $this->customerId    = $customerId;
        $this->customerGroup = $customerGroup;
    }
    
    
    /**
     * @param CustomerId    $customerId
     * @param CustomerGroup $customerGroup
     *
     * @return CustomersCustomerGroupUpdated
     */
    public static function create(CustomerId $customerId, CustomerGroup $customerGroup): CustomersCustomerGroupUpdated
    {
        
        return new self($customerId, $customerGroup);
    }
    
    
    /**
     * @return CustomerId
     */
    public function customerId(): CustomerId
    {
        return $this->customerId;
    }
    
    
    /**
     * @return CustomerGroup
     */
    public function customerGroup(): CustomerGroup
    {
        return $this->customerGroup;
    }
}