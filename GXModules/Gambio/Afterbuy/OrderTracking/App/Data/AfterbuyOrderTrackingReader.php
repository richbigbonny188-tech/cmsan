<?php
/* --------------------------------------------------------------
   AfterbuyOrderTrackingReader.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\OrderTracking\App\Data;

use Doctrine\DBAL\Connection;
use Exception;
use Gambio\Core\Configuration\Compatibility\ConfigurationStorageRepository;
use Gambio\Core\Configuration\Compatibility\ConfigurationStorageRepositoryBuilder;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\App\Data\Sender\AfterbuyRequestSender;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\Exceptions\AfterbuyNotEnabledException;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\Exceptions\AfterbuyNotInstalledException;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\Model\OrderId;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\Model\SimpleXmlRequest;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\Service\AfterbuyGlobalRepository;
use GXModules\Gambio\Afterbuy\OrderExport\Exceptions\AfterbuyResponseException;
use GXModules\Gambio\Afterbuy\OrderTracking\Exceptions\SyncTrackingLinksFailedException;
use GXModules\Gambio\Afterbuy\OrderTracking\Model\AfterbuyOrderTrackingCode;
use GXModules\Gambio\Afterbuy\OrderTracking\Model\AfterbuyOrderTrackingCodes;
use SimpleXMLElement;

/**
 * Class AfterbuyOrderTrackingReader
 *
 * @package GXModules\Gambio\Afterbuy\OrderTracking\App\Data
 */
class AfterbuyOrderTrackingReader
{
    private const AFTERBUY_ENDPOINT = 'GetSoldItems';
    
    
    private const CONFIGURATION_NAMESPACE = 'modules/gambio/afterbuy';
    
    
    private const DATE_FORMAT = 'd.m.Y H:i:s';
    
    /**
     * @var AfterbuyGlobalRepository
     */
    private AfterbuyGlobalRepository $globalRepository;
    
    
    /**
     * @var AfterbuyRequestSender
     */
    private AfterbuyRequestSender $sender;
    
    
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
    private array $abTrackingLinkShippingMethodsCache = [];
    
    
    /**
     * @var array
     */
    private array $orderExistsCache = [];
    
    
    /**
     * AfterbuyOrderTrackingReader constructor.
     *
     * @param AfterbuyGlobalRepository              $globalRepository
     * @param AfterbuyRequestSender                 $sender
     * @param ConfigurationStorageRepositoryBuilder $builder
     * @param Connection                            $connection
     */
    public function __construct(
        AfterbuyGlobalRepository              $globalRepository,
        AfterbuyRequestSender                 $sender,
        ConfigurationStorageRepositoryBuilder $builder,
        Connection                            $connection
    ) {
        $this->globalRepository = $globalRepository;
        $this->sender           = $sender;
        $this->storage          = $builder->build(self::CONFIGURATION_NAMESPACE);
        $this->connection       = $connection;
    }
    
    
    /**
     * @param string $lastTrackingSyncTime
     *
     * @return AfterbuyOrderTrackingCodes
     * @throws AfterbuyNotInstalledException|AfterbuyNotEnabledException
     * @throws AfterbuyResponseException
     */
    public function getTrackingCodes(string $lastTrackingSyncTime): AfterbuyOrderTrackingCodes
    {
        $days     = 30;
        $limit    = 100;
        $request  = $this->requestWithOrderFilter(...$this->findOrdersWithoutTrackingData($days, $limit));
        $response = $this->sender->send($request);
        $body     = $response['body'];
        
        return $this->getTrackingCodesFromResponse($body);
    }
    
    
    /**
     * Finds Afterbuy OrderIDs of up to $limit orders from the past $days days that do not currently have any tracking
     * code attached.
     *
     * @throws \Doctrine\DBAL\Exception
     */
    private function findOrdersWithoutTrackingData(int $days = 30, int $limit = 10)
    {
        $sinceDate = (new \DateTimeImmutable("{$days} days ago"))->format('Y-m-d');
        
        $qb          = $this->connection->createQueryBuilder();
        $ordersQuery = $qb->select('ao.order_id, ao.afterbuy_order_id')
            ->from('afterbuy_orders', 'ao')
            ->join('ao', 'orders', 'o', 'o.orders_id = ao.order_id')
            ->leftJoin('ao', 'orders_parcel_tracking_codes', 'ptc', 'ptc.order_id = ao.order_id')
            ->where("o.date_purchased > {$qb->createNamedParameter($sinceDate)}")
            ->andWhere('ptc.orders_parcel_tracking_code_id is null')
            ->orderBy('ao.order_id', 'desc')
            ->setFirstResult(0)
            ->setMaxResults($limit);
        $orders      = $ordersQuery->executeQuery()->fetchAllAssociative();
        if (empty($orders)) {
            return [];
        }
        
        return array_map(fn($row) => $row['afterbuy_order_id'], $orders);
    }
    
    
    /**
     * Tries to find a tracking code for the given order.
     *
     * It is required that the `afterbuy_orders` table contains a mapping from
     * the shops order id to Afterbuy order id.
     *
     * @param OrderId $orderId
     *
     * @return AfterbuyOrderTrackingCodes|null
     * @throws AfterbuyNotInstalledException|AfterbuyNotEnabledException
     * @throws AfterbuyResponseException
     */
    public function findTrackingCodesByOrderId(OrderId $orderId): ?AfterbuyOrderTrackingCodes
    {
        $afterbuyOrderId = $this->findAfterbuyOrderId($orderId);
        if (null === $afterbuyOrderId) {
            return null;
        }
        
        $request  = $this->requestWithOrderFilter($afterbuyOrderId);
        $response = $this->sender->send($request);
        $body     = $response['body'];
        
        return $this->getTrackingCodesFromResponse($body, $afterbuyOrderId);
    }
    
    
    /**
     * Returns afterbuy tracking codes from the xml response body.
     *
     * @param string $body
     *
     * @return AfterbuyOrderTrackingCodes
     */
    private function getTrackingCodesFromResponse(string $body): AfterbuyOrderTrackingCodes
    {
        $xml = simplexml_load_string($body);
        
        $result = $this->getElement('Result', $xml);
        $orders = $this->getElement('Orders', $result);
        $order  = $this->getElement('Order', $orders);
        
        $codes = [];
        if (is_iterable($order)) {
            foreach ($order as $orderData) {
                $additionalInfo = $this->getElementAsString('AdditionalInfo', $orderData);
                $orderId        = $this->getElementAsString('OrderIDAlt', $orderData);
                
                if (!empty($additionalInfo) && !empty($orderId)) {
                    $shippingInfo   = $this->getElement('ShippingInfo', $orderData);
                    $shippingMethod = $this->getElementAsString('ShippingMethod', $shippingInfo);
                    $trackingLink   = (string)$orderData->TrackingLink;
                    $trackingLink   = strtr($trackingLink,
                                            [
                                                '<-Zinfo->' => $additionalInfo,
                                                '<-zinfo->' => $additionalInfo,
                                                '<-KLPLZ->' => (string)$orderData->BuyerInfo->ShippingAddress->PostalCode,
                                            ]);
                    
                    if (!array_key_exists($orderId, $codes)) {
                        $codes[$orderId] = new AfterbuyOrderTrackingCode(new OrderId((int)$orderId),
                                                                         $additionalInfo,
                                                                         $shippingMethod,
                                                                         $trackingLink);
                    }
                }
            }
        }
        $afterbuyOrderTrackingCodes = array_values($codes);
        
        return new AfterbuyOrderTrackingCodes(...$afterbuyOrderTrackingCodes);
    }
    
    
    /**
     * @param OrderId $orderId
     *
     * @return string|null
     * @throws \Doctrine\DBAL\Exception
     */
    private function findAfterbuyOrderId(OrderId $orderId): ?string
    {
        $qb    = $this->connection->createQueryBuilder();
        $where = $qb->expr()->eq('order_id', $orderId->orderId());
        try {
            $statement = $qb->select('afterbuy_order_id')->from('afterbuy_orders')->where($where)->executeQuery();
        } catch (\Doctrine\DBAL\Exception $e) {
            return null;
        }
        
        $result = $statement->fetchAssociative();
        if ($result === false) {
            return null;
        }
        
        return $result['afterbuy_order_id'];
    }
    
    
    /**
     * Checks if an order record exists for the given tracking codes order id.
     *
     * @param AfterbuyOrderTrackingCode $trackingCode
     *
     * @return bool
     * @throws \Doctrine\DBAL\Exception
     */
    public function orderExists(AfterbuyOrderTrackingCode $trackingCode): bool
    {
        $orderId = $trackingCode->orderId();
        if (array_key_exists($orderId, $this->orderExistsCache)) {
            return $this->orderExistsCache[$orderId];
        }
        
        $qb    = $this->connection->createQueryBuilder();
        $where = $qb->expr()->eq('orders_id', $orderId);
        try {
            $statement = $qb->select('*')->from('orders')->where($where)->executeQuery();
        } catch (Exception $e) {
            return false;
        }
        $result                           = $statement->fetchAssociative();
        $this->orderExistsCache[$orderId] = $result !== false;
        
        return $this->orderExistsCache[$orderId];
    }
    
    
    /**
     * Checks if a tracking link already exists for the order.
     *
     * @param AfterbuyOrderTrackingCode $trackingCode
     *
     * @return bool
     * @throws SyncTrackingLinksFailedException
     * @throws \Doctrine\DBAL\Exception
     */
    public function trackingLinkExists(AfterbuyOrderTrackingCode $trackingCode): bool
    {
        $qb = $this->connection->createQueryBuilder();
        
        $where = "order_id = {$trackingCode->orderId()} AND tracking_code = '{$qb->createNamedParameter($trackingCode->trackingCode())}'";
        try {
            $statement = $qb->select('*')->from('orders_parcel_tracking_codes')->where($where)->executeQuery();
        } catch (\Doctrine\DBAL\Exception $e) {
            $message = "Failed to check if tracking code '{$trackingCode->trackingCode()}' for order id '{$trackingCode->orderId()}' already exists.\nError: {$e->getMessage()}";
            throw new SyncTrackingLinksFailedException($message, $e->getCode(), $e);
        }
        $result = $statement->fetchAssociative();
        
        return $result !== false;
    }
    
    
    /**
     * Returns a list of all afterbuy tracking link synchronization configurations.
     *
     * When removing the prefix `tracking_sync_shipping_methods_` from the array key, you
     * get the parcel service provider id of the shop system.
     * The result will be cached, so subsequent calls
     *
     * @return array
     */
    public function getAfterbuyTrackingSyncShippingMethods(): array
    {
        if (empty($this->abTrackingLinkShippingMethodsCache)) {
            $prefix                                   = 'tracking_sync_shipping_methods_';
            $this->abTrackingLinkShippingMethodsCache = $this->storage->getAll($prefix);
        }
        
        return $this->abTrackingLinkShippingMethodsCache;
    }
    
    
    /**
     * Tries to get a `SimpleXMLElement` from the given element by key.
     * Returns null if the key is not available.
     *
     * @param string                $key
     * @param SimpleXMLElement|null $element
     *
     * @return SimpleXMLElement|null
     */
    private function getElement(string $key, ?SimpleXMLElement $element): ?SimpleXMLElement
    {
        if (null === $element) {
            return null;
        }
        
        if (property_exists($element, $key)) {
            return $element->$key;
        }
        
        return null;
    }
    
    
    /**
     * Tries to return a xml element as string.
     * Returns null if the key is not available.
     *
     * @param string                $key
     * @param SimpleXMLElement|null $element
     *
     * @return string|null
     */
    private function getElementAsString(string $key, ?SimpleXMLElement $element): ?string
    {
        $otherElement = $this->getElement($key, $element);
        if (null !== $otherElement) {
            return (string)$otherElement;
        }
        
        return null;
    }
    
    
    /**
     * Returns the xml request body for the XML-API endpoint 'GetSoldItems'.
     *
     * @param string $lastTrackingSyncDate
     *
     * @return SimpleXmlRequest
     * @throws AfterbuyNotEnabledException
     * @throws AfterbuyNotInstalledException
     */
    private function requestWithDateFilter(string $lastTrackingSyncDate): SimpleXmlRequest
    {
        $afterbuyGlobal    = $this->globalRepository->getAfterbuyGlobal(self::AFTERBUY_ENDPOINT);
        $afterbuyGlobalXml = $afterbuyGlobal->toXmlString();
        $dateNow           = date(self::DATE_FORMAT);
        
        $xml = <<<XML
<Request>
    $afterbuyGlobalXml
    <DataFilter>
        <Filter>
            <FilterName>DateFilter</FilterName>
            <FilterValues>
                <FilterValue>ModDate</FilterValue>
                <DateFrom>$lastTrackingSyncDate</DateFrom>
                <DateTo>$dateNow</DateTo>
            </FilterValues>
        </Filter>
    </DataFilter>
</Request>
XML;
        
        return new SimpleXmlRequest($xml);
    }
    
    
    /**
     * Returns the xml request body for the XML-API endpoint 'GetSoldItems'.
     *
     * @param string ...$afterbuyOrderIds
     *
     * @return SimpleXmlRequest
     * @throws AfterbuyNotEnabledException
     * @throws AfterbuyNotInstalledException
     */
    private function requestWithOrderFilter(string ...$afterbuyOrderIds): SimpleXmlRequest
    {
        $afterbuyGlobal    = $this->globalRepository->getAfterbuyGlobal(self::AFTERBUY_ENDPOINT);
        $afterbuyGlobalXml = $afterbuyGlobal->toXmlString();
        
        $filterValues = '';
        foreach ($afterbuyOrderIds as $afterbuyOrderId) {
            $filterValues .= "<FilterValue>{$afterbuyOrderId}</FilterValue>\n";
        }
        
        $xml = <<<XML
<Request>
    $afterbuyGlobalXml
    <DataFilter>
        <Filter>
            <FilterName>OrderID</FilterName>
            <FilterValues>
                {$filterValues}
            </FilterValues>
        </Filter>
    </DataFilter>
</Request>
XML;
        
        return new SimpleXmlRequest($xml);
    }
}