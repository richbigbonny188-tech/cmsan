<?php
/* --------------------------------------------------------------
   HermesHSIShipmentsRepository.inc.php 2019-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

class HermesHSIShipmentsRepository
{
    /** @var bool */
    protected $testMode;
    
    /** @var CI_DB_query_builder */
    protected $db;
    
    protected const TABLE_NAME = 'hermeshsi_shipments';
    
    public function __construct(bool $useTestMode = false)
    {
        $this->testMode = $useTestMode;
        $this->db = StaticGXCoreLoader::getDatabaseQueryBuilder();
    }
    
    
    /**
     * @param IdType $ordersId
     * @param string $shipmentId
     *
     * @throws HermesHSIInvalidDataException
     */
    public function storeShipment(IdType $ordersId, string $shipmentId): void
    {
        if (preg_match('/\d+/', $shipmentId) !== 1) {
            throw new HermesHSIInvalidDataException('Shipment IDs must be numeric');
        }
        $data = [
            'orders_id' => $ordersId->asInt(),
            'shipment_id' => $shipmentId,
            'test_mode' => $this->testMode ? '1' : '0',
        ];
        $this->db->insert(static::TABLE_NAME, $data);
    }
    
    
    /**
     * @param IdType $ordersId
     *
     * @return array
     */
    public function retrieveAllShipmentIdsForOrder(IdType $ordersId): array
    {
        $dbRows = $this->db->get_where(static::TABLE_NAME, ['orders_id' => $ordersId->asInt(), 'test_mode' => $this->testMode ? '1' : '0'])->result_array();
        $shipmentIds = array_map(static function($row) { return $row['shipment_id']; }, $dbRows);
        return $shipmentIds;
    }
    
    
    /**
     * @param string $shipmentOrdersId
     *
     * @return IdType
     * @throws HermesHSIShipmentNotFoundException
     */
    public function findOrdersIdByShipmentOrderId(string $shipmentOrdersId): IdType
    {
        $dbRow = $this->db->get_where(static::TABLE_NAME, ['shipment_id' => $shipmentOrdersId, 'test_mode' => $this->testMode ? '1' : '0'])->row_array();
        if (empty($dbRow)) {
            throw new HermesHSIShipmentNotFoundException('no order found by given shipmentOrderId');
        }
        return new IdType((int)$dbRow['orders_id']);
    }
}
