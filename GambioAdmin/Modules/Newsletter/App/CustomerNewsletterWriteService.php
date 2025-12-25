<?php
/*--------------------------------------------------------------
   CustomerNewsletterWriteService.php 2022-11-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Newsletter\App;

use Gambio\Admin\Modules\Newsletter\Services\CustomerNewsletterFactory;
use Gambio\Admin\Modules\Newsletter\Services\CustomerNewsletterRepository;
use Gambio\Admin\Modules\Newsletter\Services\CustomerNewsletterWriteService as CustomerNewsletterWriteServiceInterface;

/**
 * Class CustomerNewsletterWriteService
 *
 * @package Gambio\Admin\Modules\Newsletter\App
 */
class CustomerNewsletterWriteService implements CustomerNewsletterWriteServiceInterface
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
    public function subscribe(int $customerId, int $adminId): void
    {
        $this->repository->subscribe($this->factory->createCustomerId($customerId), $this->factory->createCustomerId($adminId));
    }
    
    
    /**
     * @inheritDoc
     */
    public function unsubscribe(int $customerId): void
    {
        $this->repository->unsubscribe($this->factory->createCustomerId($customerId));
    }
}