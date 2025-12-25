<?php
/* --------------------------------------------------------------
   CustomerPasswordWriteService.php 2022-01-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Services;

use Gambio\Admin\Modules\Customer\Services\Exceptions\InvalidPasswordFormatException;

/**
 * Interface CustomerPasswordWriteService
 *
 * @package Gambio\Admin\Modules\Customer\Services
 */
interface CustomerPasswordWriteService
{
    /**
     * Sets a password for a given customer.
     *
     * @param int    $customerId
     * @param string $password
     *
     * @return void
     *
     * @throws InvalidPasswordFormatException
     */
    public function setCustomerPassword(int $customerId, string $password): void;
}