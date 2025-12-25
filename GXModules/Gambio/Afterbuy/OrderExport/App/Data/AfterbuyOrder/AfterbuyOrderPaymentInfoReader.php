<?php
/* --------------------------------------------------------------
   AfterbuyOrderPaymentInfoReader.php 2023-06-09
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
use Doctrine\DBAL\Exception;
use Gambio\Core\Configuration\Compatibility\ConfigurationStorageRepository;
use Gambio\Core\Configuration\Compatibility\ConfigurationStorageRepositoryBuilder;
use Gambio\Core\Configuration\Services\ConfigurationFinder;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\App\AfterbuyLogger;
use GXModules\Gambio\Afterbuy\OrderExport\Exceptions\AfterbuyOrderTotalDataNotFoundException;

/**
 * Class AfterbuyOrderPaymentInfoReader
 *
 * @package GXModules\Gambio\Afterbuy\OrderExport\App\Data\AfterbuyOrder
 */
class AfterbuyOrderPaymentInfoReader
{
    private const STORAGE_AFTERBUY_NAMESPACE = 'modules/gambio/afterbuy';
    private const CONFIG_ALIAS_SCAFFOLD      = 'configuration/MODULE_PAYMENT_%s_ALIAS';
    
    /**
     * @var ConfigurationFinder
     */
    private ConfigurationFinder $configurationFinder;
    
    
    /**
     * @var ConfigurationStorageRepository
     */
    private ConfigurationStorageRepository $storage;
    
    
    /**
     * @var AfterbuyOrderTotalReader
     */
    private AfterbuyOrderTotalReader $orderTotalReader;
    
    
    /**
     * @var Connection
     */
    private Connection $connection;
    
    
    /**
     * @var AfterbuyLogger
     */
    private AfterbuyLogger $logger;
    
    
    /**
     * AfterbuyOrderPaymentInfoReader constructor.
     *
     * @param ConfigurationFinder                   $configurationFinder
     * @param ConfigurationStorageRepositoryBuilder $storageBuilder
     * @param AfterbuyOrderTotalReader              $orderTotalReader
     * @param Connection                            $connection
     * @param AfterbuyLogger                        $logger
     */
    public function __construct(
        ConfigurationFinder                   $configurationFinder,
        ConfigurationStorageRepositoryBuilder $storageBuilder,
        AfterbuyOrderTotalReader              $orderTotalReader,
        Connection                            $connection,
        AfterbuyLogger                        $logger
    ) {
        $this->configurationFinder = $configurationFinder;
        $this->storage             = $storageBuilder->build(self::STORAGE_AFTERBUY_NAMESPACE);
        $this->orderTotalReader    = $orderTotalReader;
        $this->connection          = $connection;
        $this->logger              = $logger;
    }
    
    
    /**
     * Tries to return the payment method of the given payment class.
     * Returns $paymentClass if nothing was found.
     *
     * @param string $paymentClass
     *
     * @return string
     */
    public function getPaymentMethod(string $paymentClass): string
    {
        $key    = sprintf(self::CONFIG_ALIAS_SCAFFOLD, strtoupper($paymentClass));
        $config = $this->configurationFinder->get($key);
        if ($config === null) {
            return $paymentClass;
        }
        
        return trim($config);
    }
    
    
    /**
     * Checks if the order was already paid. If so, the order total value is returned as float.
     * If not, the method returns null.
     *
     * @param array $data
     *
     * @return float|null
     */
    public function getAlreadyPaid(array $data): ?float
    {
        $orderStatus              = $data['orders_status'];
        $alreadyPaidStatuses      = $this->storage->get('order_status_paid');
        $alreadyPaidStatusesArray = explode(',', $alreadyPaidStatuses);
        
        // determines that all order status mark orders as paid
        if (in_array('-1', $alreadyPaidStatusesArray, true) && count($alreadyPaidStatusesArray) === 1) {
            return $this->getOrderTotalPaid($data);
        }
        
        if (in_array($orderStatus, $alreadyPaidStatusesArray, true)) {
            return $this->getOrderTotalPaid($data);
        }
        
        return null;
    }
    
    
    /**
     * Fetches the payment date of the given order and returns null if the order is not marked as paid.
     *
     * @param array $data
     *
     * @return DateTimeImmutable|null
     * @throws Exception
     */
    public function getPaymentDate(array $data): ?DateTimeImmutable
    {
        if (!array_key_exists('orders_id', $data)) {
            return null;
        }
        
        $orderId    = (int)$data['orders_id'];
        $dateString = $this->getFirstPaidDate($orderId);
        if ($dateString === null) {
            return null;
        }
        
        try {
            return new DateTimeImmutable($dateString);
        } catch (\Exception $e) {
            return null;
        }
    }
    
    
    /**
     * Fetches the first date on which the order is marked as paid and returns null if nothing was found.
     *
     * To determine if the order is marked as paid, we take the Afterbuy configuration 'order_status_paid' and
     * search in the `orders_status_history` table for the first record with a matching order id containing one of
     * the 'paid' order status, sorted by the first `date_added` field (asc).
     *
     * @param int $orderId
     *
     * @return string|null
     * @throws Exception
     */
    private function getFirstPaidDate(int $orderId): ?string
    {
        $alreadyPaidStatuses = $this->storage->get('order_status_paid');
        $areAllStatusPaid    = $alreadyPaidStatuses === '-1';
        
        $qb    = $this->connection->createQueryBuilder();
        $where = $areAllStatusPaid ? "orders_id = $orderId" : "orders_id = $orderId AND orders_status_id IN ($alreadyPaidStatuses)";
        try {
            $statement = $qb->select('date_added')
                ->from('orders_status_history')
                ->where($where)
                ->orderBy('date_added')
                ->setMaxResults(1)
                ->executeQuery();
        } catch (Exception $e) {
            $message = "Failed tp fetch first paid date due to an database error:\n{$e->getMessage()}";
            $context = [
                'orderId'         => $orderId,
                'orderStatusPaid' => $alreadyPaidStatuses,
                'exception'       => [
                    'message' => $e->getMessage(),
                    'file'    => $e->getFile(),
                    'line'    => $e->getLine(),
                    'trace'   => $e->getTrace(),
                ],
            ];
            $this->logger > error($message, $context);
            
            return null;
        }
        $result = $statement->fetchAssociative();
        if ($result === false) {
            return null;
        }
        
        return $result['date_added'] ?? null;
    }
    
    
    /**
     * Fetches the order total value from the `orders_total` table for the order.
     * The order is identified by $data's element "orders_id".
     *
     * @param array $data
     *
     * @return float|null
     */
    private function getOrderTotalPaid(array $data): ?float
    {
        $orderId = (int)$data['orders_id'];
        
        try {
            $orderTotalData = $this->orderTotalReader->fetchOrderTotalsByOrderId($orderId);
            if (array_key_exists('ot_total', $orderTotalData)) {
                return (float)$orderTotalData['ot_total'];
            }
        } catch (AfterbuyOrderTotalDataNotFoundException $e) {
        }
        
        return null;
    }
}