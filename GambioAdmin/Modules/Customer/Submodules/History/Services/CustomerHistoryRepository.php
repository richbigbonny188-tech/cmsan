<?php
/*--------------------------------------------------------------
   CustomerHistoryRepository.php 2022-01-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\History\Services;

use Gambio\Admin\Modules\Customer\Submodules\History\Model\Collections\CustomerHistory;
use Gambio\Admin\Modules\Customer\Submodules\History\Model\ValueObjects\CustomerId;

/**
 * Interface CustomerHistoryRepository
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\History\Services
 */
interface CustomerHistoryRepository
{
    /**
     * Returns all available customer history entries.
     *
     * @param CustomerId $customerId
     *
     * @return CustomerHistory
     */
    public function getCustomerHistory(CustomerId $customerId): CustomerHistory;
    
    
    /**
     * Returns all available customer history entries for the given type.
     *
     * @param CustomerId $customerId
     * @param string     $type
     *
     * @return CustomerHistory
     */
    public function getCustomerHistoryForType(CustomerId $customerId, string $type): CustomerHistory;
    
    
    /**
     * Returns all available customer history entries fo
     *
     * @param CustomerHistoryReader $reader
     *
     * @return void
     */
    public function registerCustomerHistoryReader(CustomerHistoryReader $reader): void;
}