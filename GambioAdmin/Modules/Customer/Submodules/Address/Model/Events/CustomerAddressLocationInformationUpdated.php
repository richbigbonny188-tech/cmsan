<?php
/*--------------------------------------------------------------
   CustomerAddressLocationInformationUpdated.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Address\Model\Events;

use Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects\CustomerAddressId;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects\LocationInformation;

/**
 * Class CustomerAddressLocationInformationUpdated
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Address\Events
 */
class CustomerAddressLocationInformationUpdated
{
    private CustomerAddressId   $customerAddressId;
    private LocationInformation $locationInformation;
    
    
    /**
     * @param CustomerAddressId   $customerAddressId
     * @param LocationInformation $locationInformation
     */
    private function __construct(
        CustomerAddressId $customerAddressId,
        LocationInformation $locationInformation
    ) {
        $this->customerAddressId = $customerAddressId;
        $this->locationInformation = $locationInformation;
    }
    
    
    /**
     * @param CustomerAddressId   $customerAddressId
     * @param LocationInformation $locationInformation
     *
     * @return CustomerAddressLocationInformationUpdated
     */
    public static function create(
        CustomerAddressId $customerAddressId,
        LocationInformation $locationInformation
    ): CustomerAddressLocationInformationUpdated {
        
        return new self($customerAddressId, $locationInformation);
    }
    
    /**
     * @return CustomerAddressId
     */
    public function customerAddressId(): CustomerAddressId
    {
        return $this->customerAddressId;
    }
    
    
    /**
     * @return LocationInformation
     */
    public function locationInformation(): LocationInformation
    {
        return $this->locationInformation;
    }
}