<?php
/*--------------------------------------------------------------
   PaymentMethodFactory.php 2022-03-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\PaymentModule\Services;

use Gambio\Admin\Modules\PaymentModule\Model\Collections\PaymentMethods;
use Gambio\Admin\Modules\PaymentModule\Model\PaymentMethod;
use Gambio\Admin\Modules\PaymentModule\Model\ValueObjects\PaymentMethodId;

/**
 * Class PaymentMethodFactory
 *
 * @package Gambio\Admin\Modules\PaymentModule\Services
 */
class PaymentMethodFactory
{
    /**
     * Creates and returns a collection of payment methods.
     *
     * @param PaymentMethod ...$paymentMethods
     *
     * @return PaymentMethods
     */
    public function createPaymentMethods(PaymentMethod ...$paymentMethods): PaymentMethods
    {
        return PaymentMethods::create(...$paymentMethods);
    }
    
    
    /**
     * Creates and returns a payment method ID.
     *
     * @param string $paymentMethodId
     *
     * @return PaymentMethodId
     */
    public function createPaymentMethodId(string $paymentMethodId): PaymentMethodId
    {
        return PaymentMethodId::create($paymentMethodId);
    }
}