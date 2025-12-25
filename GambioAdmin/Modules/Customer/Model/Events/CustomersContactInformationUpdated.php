<?php
/*--------------------------------------------------------------
   CustomersContactInformationUpdated.php 2022-01-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Model\Events;

use Gambio\Admin\Modules\Customer\Model\ValueObjects\ContactInformation;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\CustomerId;

/**
 * Class CustomersContactInformationUpdated
 *
 * @package Gambio\Admin\Modules\Customer\Model\Events
 * @codeCoverageIgnore
 */
class CustomersContactInformationUpdated
{
    private CustomerId         $customerId;
    private ContactInformation $contactInformation;
    
    
    /**
     * @param CustomerId         $customerId
     * @param ContactInformation $contactInformation
     */
    private function __construct(CustomerId $customerId, ContactInformation $contactInformation)
    {
        $this->customerId         = $customerId;
        $this->contactInformation = $contactInformation;
    }
    
    
    /**
     * @param CustomerId         $customerId
     * @param ContactInformation $contactInformationa
     *
     * @return CustomersContactInformationUpdated
     */
    public static function create(
        CustomerId         $customerId,
        ContactInformation $contactInformation
    ): CustomersContactInformationUpdated {
        
        return new self($customerId, $contactInformation);
    }
    
    
    /**
     * @return CustomerId
     */
    public function customerId(): CustomerId
    {
        return $this->customerId;
    }
    
    
    /**
     * @return ContactInformation
     */
    public function contactInformation(): ContactInformation
    {
        return $this->contactInformation;
    }
}