<?php
/* --------------------------------------------------------------
   CustomerAddonValueReadService.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\AddonValues\App;

use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\Collections\CustomerAddonValues;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\CustomerAddonValue;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\CustomerAddonValueFactory;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\CustomerAddonValueReadService as CustomerAddonValueReadServiceInterface;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\CustomerAddonValueRepository;

/**
 * Class CustomerAddonValueReadService
 *
 * @package Gambio\Admin\Modules\CustomerAddonValue\App
 */
class CustomerAddonValueReadService implements CustomerAddonValueReadServiceInterface
{
    private CustomerAddonValueRepository $repository;
    private CustomerAddonValueFactory    $factory;
    
    
    /**
     * @param \Gambio\Admin\Modules\Customer\Submodules\AddonValues\App\CustomerAddonValueRepository $repository
     * @param CustomerAddonValueFactory                                                              $factory
     */
    public function __construct(CustomerAddonValueRepository $repository, CustomerAddonValueFactory $factory)
    {
        $this->repository = $repository;
        $this->factory    = $factory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getCustomerAddonValue(int $customerId, string $key): CustomerAddonValue
    {
        return $this->repository->getCustomerAddonValue($this->factory->createCustomerId($customerId),
                                                        $this->factory->createCustomerAddonValueKey($key));
    }
    
    
    /**
     * @inheritDoc
     */
    public function getCustomerAddonValues(int $customerId): CustomerAddonValues
    {
        return $this->repository->getCustomerAddonValues($this->factory->createCustomerId($customerId));
    }
}