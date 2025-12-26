<?php
/*--------------------------------------------------------------
   CustomerDisallowedPaymentMethodsReadService.php 2022-03-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\PaymentModule\App;

use Gambio\Admin\Modules\PaymentModule\Model\Collections\PaymentMethods;
use Gambio\Admin\Modules\PaymentModule\Services\CustomerDisallowedPaymentMethodsReadService as CustomerDisallowedPaymentMethodsReadServiceInterface;
use Gambio\Admin\Modules\PaymentModule\Services\CustomerDisallowedPaymentMethodsRepository;

/**
 * Class CustomerDisallowedPaymentMethodsReadService
 *
 * @package Gambio\Admin\Modules\PaymentModule\App
 */
class CustomerDisallowedPaymentMethodsReadService implements CustomerDisallowedPaymentMethodsReadServiceInterface
{
    private CustomerDisallowedPaymentMethodsRepository $repository;
    
    
    /**
     * @param CustomerDisallowedPaymentMethodsRepository $repository
     */
    public function __construct(CustomerDisallowedPaymentMethodsRepository $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getCustomersDisallowedPaymentMethods(int $customerId): PaymentMethods
    {
        return $this->repository->getCustomersDisallowedPaymentMethods($customerId);
    }
}