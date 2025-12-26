<?php
/*--------------------------------------------------------------
   CustomersPersonalInformationUpdated.php 2022-01-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Model\Events;

use Gambio\Admin\Modules\Customer\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\PersonalInformation;

/**
 * Class CustomersPersonalInformationUpdated
 *
 * @package Gambio\Admin\Modules\Customer\Model\Events
 * @codeCoverageIgnore
 */
class CustomersPersonalInformationUpdated
{
    private CustomerId          $customerId;
    private PersonalInformation $personalInformation;
    
    
    /**
     * @param CustomerId          $customerId
     * @param PersonalInformation $personalInformation
     */
    private function __construct(CustomerId $customerId, PersonalInformation $personalInformation)
    {
        $this->customerId          = $customerId;
        $this->personalInformation = $personalInformation;
    }
    
    
    /**
     * @param CustomerId          $customerId
     * @param PersonalInformation $personalInformation
     *
     * @return CustomersPersonalInformationUpdated
     */
    public static function create(
        CustomerId          $customerId,
        PersonalInformation $personalInformation
    ): CustomersPersonalInformationUpdated {
        
        return new self($customerId, $personalInformation);
    }
    
    
    /**
     * @return CustomerId
     */
    public function customerId(): CustomerId
    {
        return $this->customerId;
    }
    
    
    /**
     * @return PersonalInformation
     */
    public function personalInformation(): PersonalInformation
    {
        return $this->personalInformation;
    }
}