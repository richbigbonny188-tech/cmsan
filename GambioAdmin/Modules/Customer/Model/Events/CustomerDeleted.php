<?php
/*--------------------------------------------------------------
   CustomerDeleted.php 2022-01-19
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
 * Class CustomerDeleted
 *
 * @package Gambio\Admin\Modules\Customer\Model\Events
 * @codeCoverageIgnore
 */
class CustomerDeleted
{
    private CustomerId $customerId;
    
    
    /**
     * @param CustomerId $customerId
     */
    private function __construct(CustomerId $customerId)
    {
        $this->customerId = $customerId;
    }
    
    
    /**
     * @param CustomerId $customerId
     *
     * @return CustomerDeleted
     */
    public static function create(CustomerId $customerId): CustomerDeleted
    {
        return new self($customerId);
    }
    
    
    /**
     * @return CustomerId
     */
    public function customerId(): CustomerId
    {
        return $this->customerId;
    }
}