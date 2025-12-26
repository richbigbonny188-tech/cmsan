<?php
/*--------------------------------------------------------------
   CustomersBusinessInformationUpdated.php 2022-01-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Model\Events;

use Gambio\Admin\Modules\Customer\Model\ValueObjects\BusinessInformation;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\CustomerId;

/**
 * Class CustomersBusinessInformationUpdated
 *
 * @package Gambio\Admin\Modules\Customer\Model\Events
 * @codeCoverageIgnore
 */
class CustomersBusinessInformationUpdated
{
    private CustomerId          $customerId;
    private BusinessInformation $businessInformation;
    
    
    /**
     * @param CustomerId          $customerId
     * @param BusinessInformation $businessInformation
     */
    public function __construct(CustomerId $customerId, BusinessInformation $businessInformation)
    {
        $this->customerId          = $customerId;
        $this->businessInformation = $businessInformation;
    }
    
    
    /**
     * @param CustomerId          $customerId
     * @param BusinessInformation $businessInformation
     *
     * @return CustomersBusinessInformationUpdated
     */
    public static function create(
        CustomerId          $customerId,
        BusinessInformation $businessInformation
    ): CustomersBusinessInformationUpdated {
        
        return new self($customerId, $businessInformation);
    }
    
    
    /**
     * @return CustomerId
     */
    public function customerId(): CustomerId
    {
        return $this->customerId;
    }
    
    
    /**
     * @return BusinessInformation
     */
    public function businessInformation(): BusinessInformation
    {
        return $this->businessInformation;
    }
}