<?php
/*--------------------------------------------------------------
   CustomersCreditUpdated.php 2022-01-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Model\Events;

use Gambio\Admin\Modules\Customer\Model\ValueObjects\CustomerCredit;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\CustomerId;

/**
 * Class CustomersCreditUpdated
 *
 * @package Gambio\Admin\Modules\Customer\Model\Events
 * @codeCoverageIgnore
 */
class CustomersCreditUpdated
{
    private CustomerId     $customerId;
    private CustomerCredit $customerCredit;
    
    
    /**
     * @param CustomerId     $customerId
     * @param CustomerCredit $customerCredit
     */
    private function __construct(CustomerId $customerId, CustomerCredit $customerCredit)
    {
        $this->customerId     = $customerId;
        $this->customerCredit = $customerCredit;
    }
    
    
    /**
     * @param CustomerId     $customerId
     * @param CustomerCredit $customerCredit
     *
     * @return CustomersCreditUpdated
     */
    public static function create(CustomerId $customerId, CustomerCredit $customerCredit): CustomersCreditUpdated
    {
        
        return new self($customerId, $customerCredit);
    }
    
    
    /**
     * @return CustomerId
     */
    public function customerId(): CustomerId
    {
        return $this->customerId;
    }
    
    
    /**
     * @return CustomerCredit
     */
    public function customerCredit(): CustomerCredit
    {
        return $this->customerCredit;
    }
}