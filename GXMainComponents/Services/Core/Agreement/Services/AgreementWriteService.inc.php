<?php
/* --------------------------------------------------------------
   AgreementWriteService.inc.php 2018-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AgreementWriteService
 *
 * This class provides methods in order to store and delete agreements.
 *
 * @category   System
 * @package    Agreement
 * @implements AgreementWriteServiceInterface
 */
class AgreementWriteService implements AgreementWriteServiceInterface
{
    
    /**
     * @var \AgreementFactory
     */
    protected $factory;
    
    
    /**
     * AgreementWriteService constructor.
     *
     * @param \AgreementFactory $factory
     */
    public function __construct(AgreementFactory $factory)
    {
        $this->factory = $factory;
    }
    
    
    /**
     * Returns an agreement entity.
     *
     * @return Agreement
     */
    public function create()
    {
        return $this->factory->create();
    }
    
    
    /**
     * Returns an agreement customer.
     *
     * @param \StringType             $customerName  Customer name.
     * @param \CustomerEmailInterface $customerEmail Customer email.
     *
     * @return \AgreementCustomer
     */
    public function createCustomer(StringType $customerName, CustomerEmailInterface $customerEmail)
    {
        return $this->factory->createCustomer($customerName, $customerEmail);
    }
    
    
    /**
     * Stores the provided agreement.
     *
     * @param \AgreementInterface $agreement
     *
     * @return $this|\AgreementWriteServiceInterface Same instance for chained method calls.
     */
    public function store(AgreementInterface $agreement)
    {
        $agreement->store();
        
        return $this;
    }
    
    
    /**
     * Deletes the provided agreement.
     *
     * @param \AgreementInterface $agreement
     *
     * @return $this|\AgreementWriteServiceInterface Same instance for chained method calls.
     */
    public function delete(AgreementInterface $agreement)
    {
        $agreement->delete();
        
        return $this;
    }
    
}