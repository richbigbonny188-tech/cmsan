<?php
/* --------------------------------------------------------------
   WithdrawalAccessRepository.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class WithdrawalAccessRepository
 *
 * @category   System
 * @package    Withdrawal
 * @subpackage Repositories
 */
class WithdrawalAccessRepository implements WithdrawalAccessRepositoryInterface
{
    /**
     * @var \WithdrawalFactory
     */
    protected $factory;
    
    /**
     * @var \WithdrawalReaderInterface
     */
    protected $reader;
    
    
    /**
     * WithdrawalAccessRepository constructor.
     *
     * @param \WithdrawalFactory         $factory
     * @param \WithdrawalReaderInterface $reader
     */
    public function __construct(WithdrawalFactory $factory, WithdrawalReaderInterface $reader)
    {
        $this->factory = $factory;
        $this->reader  = $reader;
    }
    
    
    /**
     * Returns all withdrawal as collection.
     *
     * @return WithdrawalCollection
     */
    public function getAll()
    {
        $rawData    = $this->reader->getAll();
        $collection = $this->factory->createCollection();
        
        foreach ($rawData as $data) {
            $withdrawal = $this->factory->createEntity();
            
            $withdrawal->setWithdrawalId(new IdType($data['id']));
            $withdrawal->setWithdrawalDate(new DateTime($data['date']));
            $withdrawal->setWithdrawalContent(new StringType($data['content']));
            $withdrawal->setDateCreated(new DateTime($data['dateCreated']));
            $withdrawal->setCreatedByAdmin(new BoolType($data['createdByAdmin']));
            $withdrawal->setWithdrawalOrder($this->_createOrder($data['order']));
            
            $collection->addItem($withdrawal);
        }
        
        return $collection;
    }
    
    
    /**
     * Returns withdrawal entity by given id.
     *
     * @param \IdType $withdrawalId IdType of entity to be returned.
     *
     * @return \Withdrawal
     */
    public function getById(IdType $withdrawalId)
    {
        $data = $this->reader->getById($withdrawalId);
        
        $withdrawal = $this->factory->createEntity();
        
        $withdrawal->setWithdrawalId($withdrawalId);
        $withdrawal->setWithdrawalDate(new DateTime($data['date']));
        $withdrawal->setWithdrawalContent(new StringType($data['content']));
        $withdrawal->setDateCreated(new DateTime($data['dateCreated']));
        $withdrawal->setCreatedByAdmin(new BoolType($data['createdByAdmin']));
        $withdrawal->setWithdrawalOrder($this->_createOrder($data['order']));
        
        return $withdrawal;
    }
    
    
    /**
     * Creates with given array an withdrawalOrder.
     *
     * @param array $dataSet
     *
     * @return \WithdrawalOrder
     */
    protected function _createOrder(array $dataSet)
    {
        return $this->factory->createOrder(new IntType($dataSet['orderId']),
                                           new IntType($dataSet['customerId']),
                                           new StringType($dataSet['gender']),
                                           new StringType($dataSet['firstName']),
                                           new StringType($dataSet['lastName']),
                                           new StringType($dataSet['address']),
                                           new IntType($dataSet['postCode']),
                                           new StringType($dataSet['city']),
                                           new StringType($dataSet['country']),
                                           new StringType($dataSet['email']),
                                           new DateTime($dataSet['orderDate']),
                                           new DateTime($dataSet['deliveryDate']));
    }
}
