<?php
/*--------------------------------------------------------------
   CustomerHistoryReadService.php 2022-01-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\History\Services;

use Gambio\Admin\Modules\Customer\Submodules\History\Model\Collections\CustomerHistory;

/**
 * Interface CustomerHistoryReadService
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\History\Services
 */
interface CustomerHistoryReadService
{
    /**
     * Returns all available customer history entries.
     *
     * @param int $customerId
     *
     * @return CustomerHistory
     */
    public function getCustomerHistory(int $customerId): CustomerHistory;
    
    
    /**
     * Returns all available customer history entries for the given type.
     *
     * @param int    $customerId
     * @param string $type
     *
     * @return CustomerHistory
     */
    public function getCustomerHistoryForType(int $customerId, string $type): CustomerHistory;
}