<?php
/*--------------------------------------------------------------
   CustomerDisallowedShippingMethodsReadService.php 2022-03-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\ShippingModule\App;

use Gambio\Admin\Modules\ShippingModule\Model\Collections\ShippingMethods;
use Gambio\Admin\Modules\ShippingModule\Services\CustomerDisallowedShippingMethodsReadService as CustomerDisallowedShippingMethodsReadServiceInterface;
use Gambio\Admin\Modules\ShippingModule\Services\CustomerDisallowedShippingMethodsRepository;

/**
 * Class CustomerDisallowedShippingMethodsReadService
 *
 * @package Gambio\Admin\Modules\ShippingModule\App
 */
class CustomerDisallowedShippingMethodsReadService implements CustomerDisallowedShippingMethodsReadServiceInterface
{
    private CustomerDisallowedShippingMethodsRepository $repository;
    
    
    /**
     * @param CustomerDisallowedShippingMethodsRepository $repository
     */
    public function __construct(CustomerDisallowedShippingMethodsRepository $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getCustomersDisallowedShippingMethods(int $customerId): ShippingMethods
    {
        return $this->repository->getCustomersDisallowedShippingMethods($customerId);
    }
}