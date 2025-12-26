<?php
/*--------------------------------------------------------------
   CustomerDisallowedPaymentMethodsWriteService.php 2022-03-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\PaymentModule\Services;

use Gambio\Admin\Modules\PaymentModule\Services\Exceptions\CustomerDoesNotExistException;

/**
 * Interface CustomerDisallowedPaymentMethodsWriteService
 *
 * @package Modules\PaymentModule\Services
 */
interface CustomerDisallowedPaymentMethodsWriteService
{
    /**
     * Sets the disallowed payment methods for a given customer.
     *
     * @param int    $customerId
     * @param string ...$methodsIds
     *
     * @return void
     *
     * @throws CustomerDoesNotExistException
     */
    public function setDisallowedPaymentMethods(int $customerId, string ...$methodsIds): void;
}