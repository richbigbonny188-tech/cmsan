<?php
/*--------------------------------------------------------------
   CustomerNewsletterReadService.php 2022-03-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Newsletter\App;

use Gambio\Admin\Modules\Newsletter\Model\Collections\CustomerIds;
use Gambio\Admin\Modules\Newsletter\Services\CustomerNewsletterFactory;
use Gambio\Admin\Modules\Newsletter\Services\CustomerNewsletterReadService as CustomerNewsletterReadServiceInterface;
use Gambio\Admin\Modules\Newsletter\Services\CustomerNewsletterRepository;

/**
 * Class CustomerNewsletterReadService
 *
 * @package Gambio\Admin\Modules\Newsletter\App
 */
class CustomerNewsletterReadService implements CustomerNewsletterReadServiceInterface
{
    private CustomerNewsletterRepository $repository;
    private CustomerNewsletterFactory    $factory;
    
    
    /**
     * @param CustomerNewsletterRepository $repository
     * @param CustomerNewsletterFactory    $factory
     */
    public function __construct(CustomerNewsletterRepository $repository, CustomerNewsletterFactory $factory)
    {
        $this->repository = $repository;
        $this->factory    = $factory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getSubscribedCustomers(): CustomerIds
    {
        return $this->repository->getSubscribedCustomers();
    }
    
    
    /**
     * @inheritDoc
     */
    public function isCustomerSubscribed(int $customerId): bool
    {
        return $this->repository->isCustomerSubscribed($this->factory->createCustomerId($customerId));
    }
}