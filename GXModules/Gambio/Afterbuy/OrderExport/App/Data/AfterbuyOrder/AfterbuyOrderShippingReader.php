<?php
/* --------------------------------------------------------------
   AfterbuyOrderShippingReader.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\OrderExport\App\Data\AfterbuyOrder;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Exception;
use Gambio\Core\Configuration\Services\ConfigurationFinder;
use GXModules\Gambio\Afterbuy\OrderExport\Exceptions\AfterbuyOrderTotalDataNotFoundException;

/**
 * Class AfterbuyOrderShippingReader
 *
 * @package GXModules\Gambio\Afterbuy\OrderExport\App\Data\AfterbuyOrder
 */
class AfterbuyOrderShippingReader
{
    private const CONFIG_SCAFFOLD                   = 'configuration/MODULE_SHIPPING_%s_ALIAS';
    private const CONFIG_ORDER_STATUS_SHIPPING_DATE = 'modules/gambio/afterbuy/order_status_shipping_date';
    
    /**
     * @var ConfigurationFinder
     */
    private ConfigurationFinder $configurationFinder;
    
    
    /**
     * @var AfterbuyOrderTotalReader
     */
    private AfterbuyOrderTotalReader $orderTotalReader;
    
    
    /**
     * @var Connection
     */
    private Connection $connection;
    
    
    /**
     * AfterbuyOrderShippingReader constructor.
     *
     * @param ConfigurationFinder      $configurationFinder
     * @param AfterbuyOrderTotalReader $orderTotalReader
     * @param Connection               $connection
     */
    public function __construct(
        ConfigurationFinder      $configurationFinder,
        AfterbuyOrderTotalReader $orderTotalReader,
        Connection               $connection
    ) {
        $this->configurationFinder = $configurationFinder;
        $this->orderTotalReader    = $orderTotalReader;
        $this->connection          = $connection;
    }
    
    
    /**
     * Tries to get the shipping method name from the configuration.
     * Returns the $shippingClass if nothing was found.
     *
     * @param string $shippingClass
     *
     * @return string
     */
    public function getShippingMethodFromClass(string $shippingClass): string
    {
        $shippingClassArray = explode('_', $shippingClass);
        if (count($shippingClassArray) === 2) {
            $shippingClass = (string)array_pop($shippingClassArray);
        }
        $shippingClass = strtoupper($shippingClass);
        
        $key    = sprintf(self::CONFIG_SCAFFOLD, $shippingClass);
        $config = $this->configurationFinder->get($key);
        
        return $config ?? $shippingClass;
    }
    
    
    /**
     * Returns shipping cost for order.
     *
     * @param array $data
     *
     * @return float|null
     */
    public function getShippingCost(array $data): ?float
    {
        $orderId = (int)$data['orders_id'];
        try {
            $orderTotalData = $this->orderTotalReader->fetchOrderTotalsByOrderId($orderId);
            if (array_key_exists('ot_shipping', $orderTotalData)) {
                return (float)$orderTotalData['ot_shipping'];
            }
        } catch (AfterbuyOrderTotalDataNotFoundException $e) {
        }
        
        return null;
    }
    
    
    /**
     * Tries to fetch a delivery date by using the order id of the given data and the
     * 'order_status_shipping_date' afterbuy configuration.
     * The date value is fetched from a matching record in the orders_status_history table.
     *
     * @param array $data
     *
     * @return DateTimeImmutable|null
     * @throws \Doctrine\DBAL\Exception
     */
    public function getDeliveryDate(array $data): ?DateTimeImmutable
    {
        $orderStatus = $this->configurationFinder->get(self::CONFIG_ORDER_STATUS_SHIPPING_DATE);
        if ($orderStatus === null || $orderStatus === '-1') {
            return null;
        }
        $orderId    = (int)$data['orders_id'];
        $dateString = $this->fetchOrderStatusHistoryDate($orderId, $orderStatus);
        if ($dateString === null) {
            return null;
        }
        
        try {
            return new DateTimeImmutable($dateString);
        } catch (Exception $e) {
            return null;
        }
    }
    
    
    /**
     * Fetches the **oldest** order status history date if the given
     * $orderId and $orderStatus match to a record in 'orders_status_history'.
     * Otherwise, null is returned.
     *
     * @param int    $orderId
     * @param string $orderStatus
     *
     * @return string|null
     * @throws \Doctrine\DBAL\Exception
     */
    private function fetchOrderStatusHistoryDate(int $orderId, string $orderStatus): ?string
    {
        $qb    = $this->connection->createQueryBuilder();
        $where = "orders_id = {$qb->createNamedParameter($orderId)} AND orders_status_id = {$qb->createNamedParameter($orderStatus)}";
        
        try {
            $statement = $qb->select('*')
                ->from('orders_status_history')
                ->where($where)
                ->orderBy('date_added')
                ->setMaxResults(1)
                ->executeQuery();
        } catch (\Doctrine\DBAL\Exception $e) {
            return null;
        }
        
        $result = $statement->fetchAssociative();
        if ($result === false) {
            return null;
        }
        
        return $result['date_added'];
    }
}