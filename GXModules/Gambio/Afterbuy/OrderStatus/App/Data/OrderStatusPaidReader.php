<?php
/* --------------------------------------------------------------
   OrderStatusPaidReader.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\OrderStatus\App\Data;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Gambio\Core\Configuration\Compatibility\ConfigurationStorageRepository;
use Gambio\Core\Configuration\Compatibility\ConfigurationStorageRepositoryBuilder;
use GXModules\Gambio\Afterbuy\OrderStatus\App\Exceptions\OrderPaidStatusNotFoundException;
use GXModules\Gambio\Afterbuy\OrderStatus\App\Exceptions\OrderStatusDataCorruptedException;

/**
 * Class OrderStatusPaidReader
 *
 * @package GXModules\Gambio\Afterbuy\OrderStatus\App\Data
 */
class OrderStatusPaidReader
{
    private const AFTERBUY_CONFIGURATION_STORAGE_NAMESPACE = 'modules/gambio/afterbuy';
    
    
    private const AFTERBUY_CONFIGURATION_PAID = 'order_status_paid';
    
    
    private const AFTERBUY_CONFIGURATION_UNPAID = 'order_status_not_paid';
    
    
    private const CONFIGURATION_SEPARATOR = ',';
    
    /**
     * @var ConfigurationStorageRepository
     */
    private ConfigurationStorageRepository $storage;
    
    
    /**
     * @var Connection
     */
    private Connection $connection;
    
    
    /**
     * @var array
     */
    private array $paidStatusCache = [];
    
    
    /**
     * @var array
     */
    private array $unpaidStatusCache = [];
    
    
    /**
     * @var array
     */
    private array $unknownStatusCache = [];
    
    
    /**
     * OrderStatusPaidReader constructor.
     *
     * @param ConfigurationStorageRepositoryBuilder $builder
     * @param Connection                            $connection
     */
    public function __construct(ConfigurationStorageRepositoryBuilder $builder, Connection $connection)
    {
        $this->storage    = $builder->build(self::AFTERBUY_CONFIGURATION_STORAGE_NAMESPACE);
        $this->connection = $connection;
    }
    
    
    /**
     * Returns an array of order status representing that the order is paid.
     *
     * @return array
     * @throws OrderPaidStatusNotFoundException
     */
    public function getPaidOrderStatus(): array
    {
        if (!empty($this->paidStatusCache)) {
            return $this->paidStatusCache;
        }
        
        $paidStatus = $this->storage->get(self::AFTERBUY_CONFIGURATION_PAID);
        if (empty($paidStatus)) {
            $this->throwOrderPaidStatusNotFoundException(self::AFTERBUY_CONFIGURATION_PAID);
        }
        
        $result                = explode(self::CONFIGURATION_SEPARATOR, $paidStatus);
        $this->paidStatusCache = $result;
        
        return explode(self::CONFIGURATION_SEPARATOR, $paidStatus);
    }
    
    
    /**
     * Returns an array of order status representing that the order is unpaid.
     *
     * @return array
     */
    public function getUnpaidOrderStatus(): array
    {
        if (!empty($this->unpaidStatusCache)) {
            return $this->unpaidStatusCache;
        }
        $unpaidStatus = $this->storage->get(self::AFTERBUY_CONFIGURATION_UNPAID);
        
        $result                  = explode(self::CONFIGURATION_SEPARATOR, $unpaidStatus);
        $this->unpaidStatusCache = $result;
        
        return $result;
    }
    
    
    /**
     * Returns an array of order status representing that the paid status is unknown.
     *
     * @return array
     * @throws OrderPaidStatusNotFoundException
     * @throws OrderStatusDataCorruptedException
     * @throws Exception
     */
    public function getUnknownOrderStatus(): array
    {
        if (!empty($this->unknownStatusCache)) {
            return $this->unknownStatusCache;
        }
        
        $orderStatus       = $this->getOrderStatusTableData();
        $paidOrderStatus   = $this->getPaidOrderStatus();
        $unpaidOrderStatus = $this->getUnpaidOrderStatus();
        
        $this->unknownStatusCache = array_diff($orderStatus, $paidOrderStatus, $unpaidOrderStatus);
        
        return $this->unknownStatusCache;
    }
    
    
    /**
     * Fetches all order status from the related table, grouped by id (to filter language specific entries).
     * The return array only contains the order status id as value.
     *
     * @return array
     * @throws OrderPaidStatusNotFoundException Is thrown when no order status data was found.
     * @throws OrderStatusDataCorruptedException Is thrown when database query fails.
     * @throws Exception
     */
    private function getOrderStatusTableData(): array
    {
        $qb = $this->connection->createQueryBuilder();
        try {
            $statement = $qb->select('orders_status_id')
                ->from('orders_status')
                ->groupBy('orders_status_id')
                ->executeQuery();
        } catch (Exception $e) {
            $message = "Failed to fetch all order status from the database table 'orders_status'. Error: {$e->getMessage()}";
            throw new OrderStatusDataCorruptedException($message, 500, $e);
        }
        
        $result = $statement->fetchAllAssociative();
        if (empty($result)) {
            $message = "Empty table 'orders_status'. Unable to get any order status";
            throw new OrderPaidStatusNotFoundException($message);
        }
        $cb = static fn(array $data): string => $data['orders_status_id'];
        
        return array_map($cb, $result);
    }
    
    
    /**
     * Throws an `OrderStatusPaidNotFound` exception if the configuration
     * values are not available. This can be the case when the module was installed for the first time,
     * but no configurations where entered.
     *
     * @param string $config
     *
     * @throws OrderPaidStatusNotFoundException
     */
    private function throwOrderPaidStatusNotFoundException(string $config): void
    {
        $namespace = self::AFTERBUY_CONFIGURATION_STORAGE_NAMESPACE;
        $key       = self::AFTERBUY_CONFIGURATION_STORAGE_NAMESPACE . '/' . $config;
        $message   = "Can not find Afterbuy configuration '$config' in ConfigurationStorage with '$namespace' namespace (key = $key).";
        throw new OrderPaidStatusNotFoundException($message);
    }
}