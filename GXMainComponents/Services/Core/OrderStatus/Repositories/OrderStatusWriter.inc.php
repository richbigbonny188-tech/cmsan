<?php

/* --------------------------------------------------------------
   OrderStatusWriter.inc.php 2017-03-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class OrderStatusWriter
 *
 * @category   System
 * @package    OrderStatus
 * @subpackage Repositories
 */
class OrderStatusWriter implements OrderStatusWriterInterface
{
    /**
     * @var CI_DB_query_builder
     */
    protected $queryBuilder;
    
    /**
     * @var string
     */
    protected $table = 'orders_status';
    
    /**
     * @var string
     */
    protected $id = 'orders_status_id';
    
    /**
     * @var LanguageProvider
     */
    protected $languageProvider;
    
    
    /**
     * OrderStatusWriter constructor.
     *
     * @param \CI_DB_query_builder $queryBuilder     Active record instance for data access.
     * @param \LanguageProvider    $languageProvider Instance to access language code and ID's.
     */
    public function __construct(CI_DB_query_builder $queryBuilder, LanguageProvider $languageProvider)
    {
        $this->queryBuilder     = $queryBuilder;
        $this->languageProvider = $languageProvider;
    }
    
    
    /**
     * Creates a new order status.
     *
     * @param \OrderStatus $orderStatus Order status to be created.
     *
     * @return int Id of new order status entity.
     * @throws OrderStatusIdExistsException If ::getId method of $orderStatus returns not null.
     */
    public function createOrderStatus(OrderStatus $orderStatus)
    {
        if (null !== $orderStatus->getId()) {
            throw new OrderStatusIdExistsException('Invalid order status entity with existing id "'
                                                   . $orderStatus->getId() . '" provided. (Please use update method)');
        }
        
        $maxOrdersStatusId  = $this->queryBuilder->select($this->id)
                                  ->from($this->table)
                                  ->where('`' . $this->id . '` = (SELECT MAX(`' . $this->id . '`) FROM `' . $this->table
                                          . '`)',
                                          null,
                                          false)
                                  ->get()
                                  ->row_array()[$this->id];
        $nextOrdersStatusId = (int)$maxOrdersStatusId + 1;
        foreach ($orderStatus->getNames() as $languageCode => $orderStatusName) {
            $languageId = $this->languageProvider->getIdByCode(MainFactory::create('LanguageCode',
                                                                                   new StringType($languageCode)));
            $this->queryBuilder->insert('orders_status',
                                        [
                                            'orders_status_id'   => $nextOrdersStatusId,
                                            'language_id'        => $languageId,
                                            'orders_status_name' => $orderStatusName,
                                            'color'              => $orderStatus->getColor()
                                        ]);
        }
        
        return $nextOrdersStatusId;
    }
    
    
    /**
     * Updates the given order status entity.
     *
     * @param \OrderStatus $orderStatus Order status to be update.
     *
     * @return $this|OrderStatusWriterInterface Same instance for chained method calls.
     * @throws OrderStatusIdNotExistsException If ::getId method of $orderStatus returns null.
     */
    public function updateOrderStatus(OrderStatus $orderStatus)
    {
        if (null === $orderStatus->getId()) {
            throw new OrderStatusIdNotExistsException('Invalid order status entity provided.');
        }
        
        foreach ($orderStatus->getNames() as $languageCode => $orderStatusName) {
            $escapedCode = new StringType(str_replace('\'', '', $this->queryBuilder->escape($languageCode)));
            $languageId  = $this->languageProvider->getIdByCode(MainFactory::create('LanguageCode', $escapedCode));
            
            $this->queryBuilder->update($this->table,
                                        [
                                            'orders_status_name' => $orderStatusName,
                                            'color'              => $orderStatus->getColor()
                                        ],
                                        '`' . $this->id . '` = "' . $this->queryBuilder->escape($orderStatus->getId())
                                        . '" AND `language_id` = "' . $languageId . '"');
        }
        
        return $this;
    }
}