<?php

/* --------------------------------------------------------------
   OrderStatusRepository.inc.php 2017-03-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class OrderStatusRepository
 *
 * @category   System
 * @package    OrderStatus
 * @subpackage Repositories
 */
class OrderStatusRepository implements OrderStatusRepositoryInterface
{
    /**
     * @var OrderStatusFactoryInterface
     */
    protected $factory;
    
    /**
     * @var OrderStatusReaderInterface
     */
    protected $reader;
    
    /**
     * @var OrderStatusWriterInterface
     */
    protected $writer;
    
    /**
     * @var \OrderStatusDeleterInterface
     */
    protected $deleter;
    
    /**
     * @var \LanguageProvider
     */
    protected $languageProvider;
    
    
    /**
     * OrderStatusRepository constructor.
     *
     * @param \OrderStatusFactoryInterface $factory          Factory instance to create order status entities.
     * @param \OrderStatusReaderInterface  $reader           Reader instance to fetch data from the storage.
     * @param \OrderStatusWriterInterface  $writer           Writer instance to add or update data in the storage.
     * @param \OrderStatusDeleterInterface $deleter          Deleter instance to remove data from the storage.
     * @param \LanguageProvider            $languageProvider Provider instance to get language codes from language ids.
     */
    public function __construct(
        OrderStatusFactoryInterface $factory,
        OrderStatusReaderInterface $reader,
        OrderStatusWriterInterface $writer,
        OrderStatusDeleterInterface $deleter,
        LanguageProvider $languageProvider
    ) {
        $this->factory          = $factory;
        $this->reader           = $reader;
        $this->writer           = $writer;
        $this->deleter          = $deleter;
        $this->languageProvider = $languageProvider;
    }
    
    
    /**
     * Returns a collection with all order status.
     *
     * @return OrderStatusCollection Collection of all order status resources.
     */
    public function findAll()
    {
        $orderStatusData = $this->reader->getAllOrderStatus();
        $orderStatuses   = [];
        
        foreach ($orderStatusData as $data) {
            $orderStatus = $this->factory->createOrderStatus(new IntType($data['id']));
            foreach ($data['names'] as $languageId => $name) {
                $languageCode = $this->languageProvider->getCodeById(new IdType($languageId));
                $orderStatus->setName($languageCode, new StringType($name));
            }
            
            $orderStatus->setColor(new StringType($data['color']));
            $orderStatuses[] = $orderStatus;
        }
        
        return $this->factory->createOrderStatusCollection($orderStatuses);
    }
    
    
    /**
     * Returns an order status entity by the given order status id. If no data was found, null is returned.
     *
     * @param \IntType $orderStatusId Id of expected OrderStatus entity.
     *
     * @return OrderStatus|null Whether the found order status entity or null.
     */
    public function find(IntType $orderStatusId)
    {
        $data = $this->reader->getOrderStatusById($orderStatusId);
        if (count($data) === 0) {
            return null;
        }
        
        $orderStatus = $this->factory->createOrderStatus($orderStatusId);
        foreach ($data['names'] as $languageId => $name) {
            $orderStatus->setName($this->languageProvider->getCodeById(new IdType($languageId)), new StringType($name));
        }
        $orderStatus->setColor(new StringType($data['color']));
        
        return $orderStatus;
    }
    
    
    /**
     * Returns an order status entity by the given order status id. If no data was found, an exception is thrown.
     *
     * @param \IntType $orderStatusId Id of expected OrderStatus entity.
     *
     * @return OrderStatus Expected order status entity.
     * @throws OrderStatusNotFoundException If expected order status entity was not found by the given id.
     */
    public function get(IntType $orderStatusId)
    {
        $orderStatus = $this->find($orderStatusId);
        
        if (null === $orderStatus) {
            throw new OrderStatusNotFoundException('Order status with id "' . $orderStatusId->asInt() . '" not found.');
        }
        
        return $orderStatus;
    }
    
    
    /**
     * Creates a new order status.
     *
     * @param \OrderStatus $orderStatus Order status entity with new data.
     *
     * @return int Id of new order status entity.
     */
    public function create(OrderStatus $orderStatus)
    {
        return $this->writer->createOrderStatus($orderStatus);
    }
    
    
    /**
     * Updates an order status.
     *
     * @param \OrderStatus $orderStatus Order status entity with updated data.
     *
     * @return $this|OrderStatusRepositoryInterface Same instance for chained method calls.
     */
    public function update(OrderStatus $orderStatus)
    {
        $this->writer->updateOrderStatus($orderStatus);
        
        return $this;
    }
    
    
    /**
     * Removes an order status.
     *
     * @param \IntType $orderStatusId Id of order status entity to be removed.
     *
     * @return $this|OrderStatusRepositoryInterface Same instance for chained method calls.
     */
    public function remove(IntType $orderStatusId)
    {
        $this->deleter->removeOrderStatus($orderStatusId);
        
        return $this;
    }
}