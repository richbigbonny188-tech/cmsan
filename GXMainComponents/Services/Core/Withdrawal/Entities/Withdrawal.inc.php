<?php
/* --------------------------------------------------------------
   Withdrawal.inc.php 2018-01-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class Withdrawal
 *
 * @category   System
 * @package    Withdrawal
 * @subpackage Entities
 */
class Withdrawal implements WithdrawalInterface
{
    /**
     * @var int
     */
    protected $withdrawalId = 0;
    
    /**
     * @var \DateTime
     */
    protected $withdrawalDate;
    
    /**
     * @var string
     */
    protected $withdrawalContent = '';
    
    /**
     * @var \DateTime
     */
    protected $withdrawalDateCreated;
    
    /**
     * @var bool
     */
    protected $createdByAdmin = false;
    
    /**
     * @var \WithdrawalOrderInterface
     */
    protected $withdrawalOrder;
    
    /**
     * @var \WithdrawalRepositoryInterface
     */
    protected $repository;
    
    
    /**
     * Withdrawal constructor.
     *
     * @param \WithdrawalRepositoryInterface $repository
     */
    public function __construct(WithdrawalRepositoryInterface $repository)
    {
        $this->repository      = $repository;
        $this->withdrawalOrder = MainFactory::create('WithdrawalOrder',
                                                     new IntType(1),
                                                     new IntType(2),
                                                     new StringType('m'),
                                                     new StringType('First name'),
                                                     new StringType('Last name'),
                                                     new StringType('Customer Street address'),
                                                     new IntType(26222),
                                                     new StringType('Customer City'),
                                                     new StringType('customer Country'),
                                                     new StringType('Customer Email'),
                                                     new DateTime(),
                                                     new DateTime());
        $this->withdrawalDate  = $this->withdrawalDateCreated = new DateTime();
    }
    
    
    /**
     * Saves withdrawal in database.
     *
     * @return $this|\WithdrawalInterface Same instance for chained method calls.
     */
    public function store()
    {
        $this->repository->store($this);
        
        return $this;
    }
    
    
    /**
     * Deletes withdrawal from database.
     *
     * @return $this|\WithdrawalInterface Same instance for chained method calls.
     */
    public function delete()
    {
        $this->repository->delete($this);
        
        return $this;
    }
    
    
    /**
     * Returns the withdrawal id.
     *
     * @return int Withdrawal id.
     */
    public function getWithdrawalId()
    {
        return $this->withdrawalId;
    }
    
    
    /**
     * Sets the withdrawal id.
     *
     * @param \IdType $withdrawalId The withdrawal id to be set.
     *
     * @return $this|\WithdrawalInterface Same instance for chained method calls.
     */
    public function setWithdrawalId(IdType $withdrawalId)
    {
        $this->withdrawalId = $withdrawalId->asInt();
        
        return $this;
    }
    
    
    /**
     * Returns the withdrawal datetime.
     *
     * @return \DateTime
     */
    public function getWithdrawalDate()
    {
        return $this->withdrawalDate;
    }
    
    
    /**
     * sets the datetime.
     *
     * @param \DateTime $withdrawalDate Date to be set.
     *
     * @return $this|\WithdrawalInterface Same instance for chained method calls.
     */
    public function setWithdrawalDate(\DateTime $withdrawalDate)
    {
        $this->withdrawalDate = $withdrawalDate;
        
        return $this;
    }
    
    
    /**
     * Returns the content.
     *
     * @return string
     */
    public function getWithdrawalContent()
    {
        return $this->withdrawalContent;
    }
    
    
    /**
     * Sets the withdrawal content.
     *
     * @param \StringType $withdrawalContent Withdrawal content to be set.
     *
     * @return $this|\WithdrawalInterface Same instance for chained method calls.
     */
    public function setWithdrawalContent(StringType $withdrawalContent)
    {
        $this->withdrawalContent = $withdrawalContent->asString();
        
        return $this;
    }
    
    
    /**
     * Returns the withdrawal created datetime.
     *
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->withdrawalDateCreated;
    }
    
    
    /**
     * Sets the withdrawal created datetime.
     *
     * @param \DateTime $dateCreated Created date to be set.
     *
     * @return $this|\WithdrawalInterface Same instance for chained method calls.
     */
    public function setDateCreated(\DateTime $dateCreated)
    {
        $this->withdrawalDateCreated = $dateCreated;
        
        return $this;
    }
    
    
    /**
     * Checks if withdrawal by admin created or not.
     *
     * @return bool
     */
    public function getCreatedByAdmin()
    {
        return $this->createdByAdmin;
    }
    
    
    /**
     * Sets whether the withdrawal by admin created or not.
     *
     * @param \BoolType $createdByAdmin
     *
     * @return $this|\WithdrawalInterface Same instance for chained method calls.
     */
    public function setCreatedByAdmin(BoolType $createdByAdmin)
    {
        $this->createdByAdmin = $createdByAdmin->asBool();
        
        return $this;
    }
    
    
    /**
     * Returns the withdrawal Order.
     *
     * @return \WithdrawalOrderInterface
     */
    public function getWithdrawalOrder()
    {
        return $this->withdrawalOrder;
    }
    
    
    /**
     * Sets the withdrawal order.
     *
     * @param \WithdrawalOrderInterface $withdrawalOrder
     *
     * @return $this|\WithdrawalInterface Same instance for chained method calls.
     */
    public function setWithdrawalOrder(WithdrawalOrderInterface $withdrawalOrder)
    {
        $this->withdrawalOrder = $withdrawalOrder;
        
        return $this;
    }
}