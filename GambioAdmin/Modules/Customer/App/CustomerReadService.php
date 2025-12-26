<?php
/*--------------------------------------------------------------
   CustomerReadService.php 2022-07-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\App;

use Gambio\Admin\Modules\Customer\Model\Collections\Customers;
use Gambio\Admin\Modules\Customer\Model\Customer;
use Gambio\Admin\Modules\Customer\Services\CustomerFactory;
use Gambio\Admin\Modules\Customer\Services\CustomerReadService as CustomerReadServiceInterface;
use Gambio\Admin\Modules\Customer\Services\CustomerRepository;
use Gambio\Admin\Modules\Customer\Services\Exceptions\CustomerEmailAddressMustBeUniqueException;
use Gambio\Admin\Modules\Customer\Services\Exceptions\EmailAddressIsInvalidException;

/**
 * Class CustomerReadService
 *
 * @package Gambio\Admin\Modules\Customer\App
 */
class CustomerReadService implements CustomerReadServiceInterface
{
    private CustomerFactory    $factory;
    private CustomerRepository $repository;
    
    
    /**
     * @param CustomerFactory    $factory
     * @param CustomerRepository $repository
     */
    public function __construct(
        CustomerFactory    $factory,
        CustomerRepository $repository
    ) {
        $this->factory    = $factory;
        $this->repository = $repository;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getCustomers(): Customers
    {
        return $this->repository->getCustomers();
    }
    
    
    /**
     * @inheritDoc
     */
    public function getGuestAccounts(): Customers
    {
        return $this->repository->getGuestAccounts();
    }
    
    
    /**
     * @inheritDoc
     */
    public function getCustomerById(int $id): Customer
    {
        return $this->repository->getCustomerById($this->factory->createCustomerId($id));
    }
    
    
    /**
     * @inheritDoc
     */
    public function validateEmailAddress(string $email): bool
    {
        return $this->repository->validateEmailAddress($email);
    }
}