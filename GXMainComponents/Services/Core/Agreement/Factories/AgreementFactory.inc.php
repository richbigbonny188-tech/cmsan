<?php

/**
 * Class AgreementFactory
 *
 * @category   System
 * @package    Agreement
 * @subpackage Factories
 */
class AgreementFactory
{
    /**
     * @var \AgreementRepository
     */
    protected $repository;
    
    
    /**
     * AgreementFactory constructor.
     *
     * @param \AgreementRepositoryInterface $repository
     */
    public function __construct(AgreementRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * Creates an agreement.
     *
     * @return \Agreement
     */
    public function create()
    {
        return new Agreement($this->repository);
    }
    
    
    /**
     * Returns a new instance of a AgreementCustomer.
     *
     * @param \StringType             $customerName  Customer name.
     * @param \CustomerEmailInterface $customerEmail Customer email.
     *
     * @return \AgreementCustomer
     */
    public function createCustomer(StringType $customerName, CustomerEmailInterface $customerEmail)
    {
        return new AgreementCustomer($customerName, $customerEmail);
    }
    
    
    /**
     * Creates an agreement collection.
     *
     * @return \AgreementCollection
     */
    public function createCollection()
    {
        return new AgreementCollection();
    }
}