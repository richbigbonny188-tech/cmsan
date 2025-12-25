<?php
/*--------------------------------------------------------------
   CustomerAddressPersonalInformationUpdated.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Address\Model\Events;

use Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects\CustomerAddressId;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects\PersonalInformation;

/**
 * Class CustomerAddressPersonalInformationUpdated
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Address\Events
 */
class CustomerAddressPersonalInformationUpdated
{
    private CustomerAddressId   $customerAddressId;
    private PersonalInformation $personalInformation;
    
    
    /**
     * @param CustomerAddressId   $customerAddressId
     * @param PersonalInformation $personalInformation
     */
    private function __construct(
        CustomerAddressId   $customerAddressId,
        PersonalInformation $personalInformation
    ) {
        $this->customerAddressId   = $customerAddressId;
        $this->personalInformation = $personalInformation;
    }
    
    
    /**
     * @param CustomerAddressId   $customerAddressId
     * @param PersonalInformation $personalInformation
     *
     * @return CustomerAddressPersonalInformationUpdated
     */
    public static function create(
        CustomerAddressId   $customerAddressId,
        PersonalInformation $personalInformation
    ): CustomerAddressPersonalInformationUpdated {
        
        return new self($customerAddressId, $personalInformation);
    }
    
    /**
     * @return CustomerAddressId
     */
    public function customerAddressId(): CustomerAddressId
    {
        return $this->customerAddressId;
    }
    
    
    /**
     * @return PersonalInformation
     */
    public function personalInformation(): PersonalInformation
    {
        return $this->personalInformation;
    }
}