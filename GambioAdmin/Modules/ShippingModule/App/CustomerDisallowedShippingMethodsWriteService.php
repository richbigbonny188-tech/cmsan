<?php
/*--------------------------------------------------------------
   CustomerDisallowedShippingMethodsWriteService.php 2022-01-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\ShippingModule\App;

use Gambio\Admin\Modules\ShippingModule\Services\CustomerDisallowedShippingMethodsRepository;
use Gambio\Admin\Modules\ShippingModule\Services\CustomerDisallowedShippingMethodsWriteService as CustomerDisallowedShippingMethodsWriteServiceInterface;
use Gambio\Admin\Modules\ShippingModule\Services\ShippingMethodFactory;

/**
 * Class CustomerDisallowedShippingMethodsWriteService
 *
 * @package Gambio\Admin\Modules\ShippingModule\App
 */
class CustomerDisallowedShippingMethodsWriteService implements CustomerDisallowedShippingMethodsWriteServiceInterface
{
    private CustomerDisallowedShippingMethodsRepository $repository;
    private ShippingMethodFactory                       $factory;
    
    
    /**
     * @param CustomerDisallowedShippingMethodsRepository $repository
     * @param ShippingMethodFactory                       $factory
     */
    public function __construct(
        CustomerDisallowedShippingMethodsRepository $repository,
        ShippingMethodFactory                       $factory
    ) {
        $this->repository = $repository;
        $this->factory    = $factory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function setDisallowedShippingMethods(int $customerId, string ...$shippingMethodIds): void
    {
        $shippingMethodIds = array_map([$this->factory, 'createShippingMethodId'], $shippingMethodIds);
        
        $this->repository->setDisallowedShippingMethods($customerId, ...$shippingMethodIds);
    }
    
    
}