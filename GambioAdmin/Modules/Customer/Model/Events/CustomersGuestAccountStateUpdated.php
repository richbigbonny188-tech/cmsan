<?php
/*--------------------------------------------------------------
   CustomersGuestAccountStateUpdated.php 2022-01-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Model\Events;

use Gambio\Admin\Modules\Customer\Model\ValueObjects\CustomerId;

/**
 * Class CustomersGuestAccountStateUpdated
 *
 * @package Gambio\Admin\Modules\Customer\Model\Events
 * @codeCoverageIgnore
 */
class CustomersGuestAccountStateUpdated
{
    private CustomerId $customerId;
    private bool       $guestAccountState;
    
    
    /**
     * @param CustomerId $customerId
     * @param bool       $isGuestAccount
     */
    private function __construct(CustomerId $customerId, bool $isGuestAccount)
    {
        $this->customerId        = $customerId;
        $this->guestAccountState = $isGuestAccount;
    }
    
    
    /**
     * @param CustomerId $customerId
     * @param bool       $isGuestAccount
     *
     * @return CustomersGuestAccountStateUpdated
     */
    public static function create(CustomerId $customerId, bool $isGuestAccount): CustomersGuestAccountStateUpdated
    {
        return new self($customerId, $isGuestAccount);
    }
    
    
    /**
     * @return CustomerId
     */
    public function customerId(): CustomerId
    {
        return $this->customerId;
    }
    
    
    /**
     * @return bool
     */
    public function guestAccountState(): bool
    {
        return $this->guestAccountState;
    }
}