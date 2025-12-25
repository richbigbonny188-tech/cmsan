<?php
/*--------------------------------------------------------------
   CustomerDisallowedPaymentMethodsWriteService.php 2022-01-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\PaymentModule\App;

use Gambio\Admin\Modules\PaymentModule\Services\CustomerDisallowedPaymentMethodsRepository;
use Gambio\Admin\Modules\PaymentModule\Services\CustomerDisallowedPaymentMethodsWriteService as CustomerDisallowedPaymentMethodsWriteServiceInterface;
use Gambio\Admin\Modules\PaymentModule\Services\PaymentMethodFactory;

/**
 * Class CustomerDisallowedPaymentMethodsWriteService
 *
 * @package Gambio\Admin\Modules\PaymentModule\App
 */
class CustomerDisallowedPaymentMethodsWriteService implements CustomerDisallowedPaymentMethodsWriteServiceInterface
{
    private CustomerDisallowedPaymentMethodsRepository $repository;
    private PaymentMethodFactory                       $factory;
    
    
    /**
     * @param CustomerDisallowedPaymentMethodsRepository $repository
     * @param PaymentMethodFactory                       $factory
     */
    public function __construct(
        CustomerDisallowedPaymentMethodsRepository $repository,
        PaymentMethodFactory                       $factory
    ) {
        $this->repository = $repository;
        $this->factory    = $factory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function setDisallowedPaymentMethods(int $customerId, string ...$methodsIds): void
    {
        $methodsIds = array_map([$this->factory, 'createPaymentMethodId'], $methodsIds);
        
        $this->repository->setDisallowedPaymentMethods($customerId, ...$methodsIds);
    }
}