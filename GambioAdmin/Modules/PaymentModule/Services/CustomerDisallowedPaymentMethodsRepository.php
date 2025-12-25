<?php
/*--------------------------------------------------------------
   CustomerDisallowedPaymentMethodsRepository.php 2022-03-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\PaymentModule\Services;

use Gambio\Admin\Modules\PaymentModule\Model\Collections\PaymentMethods;
use Gambio\Admin\Modules\PaymentModule\Model\ValueObjects\PaymentMethodId;
use Gambio\Admin\Modules\PaymentModule\Services\Exceptions\CustomerDoesNotExistException;

/**
 * Interface CustomerDisallowedPaymentMethodsRepository
 *
 * @package Gambio\Admin\Modules\PaymentModule\Services
 */
interface CustomerDisallowedPaymentMethodsRepository
{
    /**
     * Returns all disallowed payment methods for a given customer.
     *
     * @param int $customerId
     *
     * @return PaymentMethods
     *
     * @throws CustomerDoesNotExistException
     */
    public function getCustomersDisallowedPaymentMethods(int $customerId): PaymentMethods;
    
    
    /**
     * Sets the disallowed payment methods for a given customer.
     *
     * @param int             $customerId
     * @param PaymentMethodId ...$methodsIds
     *
     * @return void
     * @throws CustomerDoesNotExistException
     */
    public function setDisallowedPaymentMethods(int $customerId, PaymentMethodId ...$methodsIds): void;
}