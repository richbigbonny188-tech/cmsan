<?php
/* --------------------------------------------------------------
   OrderDataFacade.php 2024-01-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2024 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\DHLReturns\App\Data;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\ParameterType;

class OrderDataFacade
{
    /**
     * @var Connection
     */
    private Connection $dbConnection;
    
    
    /**
     * @param Connection $dbConnection
     */
    public function __construct(Connection $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }
    
    
    /**
     * @throws Exception
     */
    public function getOrderData(int $orderId): array
    {
        $orderData = [
            'orderId'                 => $orderId,
            'customers_email_address' => '',
            'deliveryAddress'         => [
                'name'               => '',
                'firstname'          => '',
                'lastname'           => '',
                'company'            => '',
                'street_address'     => '',
                'house_number'       => '',
                'additional_info'    => '',
                'suburb'             => '',
                'city'               => '',
                'postcode'           => '',
                'country_iso_code_2' => 'DE',
                'country_iso_code_3' => 'DEU',
            ],
        ];
        
        $orderQuery     = "SELECT `customers_email_address`, `delivery_name`, `delivery_firstname`, `delivery_lastname`,
            `delivery_company`, `delivery_street_address`, `delivery_house_number`, `delivery_additional_info`,
            `delivery_suburb`, `delivery_postcode`, `delivery_city`, `delivery_country_iso_code_2`,
            `c`.`countries_iso_code_3` AS `delivery_country_iso_code_3`,
            `order_total_weight`,
            `o`.`currency`, `ot`.`value`, (`ot`.`value` / `cur`.`value`) as `value_in_eur`
            FROM `orders` `o`
            JOIN `countries` `c` on `c`.`countries_iso_code_2` = `o`.`delivery_country_iso_code_2`
            JOIN `orders_total` `ot` on `ot`.`orders_id` = `o`.`orders_id` AND `ot`.`class`= 'ot_total'
            JOIN `currencies` `cur` on `cur`.`code` = `o`.`currency`
            WHERE `o`.`orders_id` = :orders_id";
        $orderStatement = $this->dbConnection->prepare($orderQuery);
        $orderStatement->bindvalue('orders_id', $orderId, ParameterType::INTEGER);
        $orderRow = $orderStatement->executeQuery()->fetchAssociative();
        
        $orderData['customers_email_address'] = $orderRow['customers_email_address'];
        $orderData['deliveryAddress']         = [
            'name'               => $orderRow['delivery_name'],
            'firstname'          => $orderRow['delivery_firstname'],
            'lastname'           => $orderRow['delivery_lastname'],
            'company'            => $orderRow['delivery_company'],
            'street_address'     => $orderRow['delivery_street_address'],
            'house_number'       => $orderRow['delivery_house_number'],
            'additional_info'    => $orderRow['delivery_additional_info'],
            'suburb'             => $orderRow['delivery_suburb'],
            'city'               => $orderRow['delivery_city'],
            'postcode'           => $orderRow['delivery_postcode'],
            'country_iso_code_2' => $orderRow['delivery_country_iso_code_2'],
            'country_iso_code_3' => $orderRow['delivery_country_iso_code_3'],
        ];
        
        if ($orderData['deliveryAddress']['house_number'] === '') {
            if (preg_match("/(?'street'.*?)\s+(?'house'\d.*)/", $orderRow['delivery_street_address'], $matches) === 1) {
                $orderData['deliveryAddress']['street_address'] = $matches['street'];
                $orderData['deliveryAddress']['house_number']   = $matches['house'];
            }
        }
        
        $orderData['value']         = number_format((float)$orderRow['value_in_eur'], 2);
        $orderData['weightInGrams'] = (int)((float)$orderRow['order_total_weight'] * 1000);
        
        $shipmentQuery     = "SELECT `tracking_code`, `parcel_service_name`
            FROM `orders_parcel_tracking_codes` WHERE `order_id` = :orders_id LIMIT 1";
        $shipmentStatement = $this->dbConnection->prepare($shipmentQuery);
        $shipmentStatement->bindvalue('orders_id', $orderId);
        $shipmentRow    = $shipmentStatement->executeQuery()->fetch();
        $shipmentNumber = $shipmentRow['tracking_code'] ?? '';
        $operator       = $shipmentRow['parcel_service_name'] ?? '';
        
        $invoiceQuery     = "SELECT `invoice_number`, `invoice_date` FROM `invoices` WHERE `order_id` = :order_id";
        $invoiceStatement = $this->dbConnection->prepare($invoiceQuery);
        $invoiceStatement->bindValue('order_id', $orderId);
        $invoiceRow    = $invoiceStatement->executeQuery()->fetch();
        $invoiceNumber = $invoiceRow['invoice_number'] ?? '';
        $invoiceDate   = isset($invoiceRow['invoice_date']) ? date('Y-m-d',
                                                                   strtotime($invoiceRow['invoice_date'])) : '';
        
        $positions          = [];
        $positionsQuery     = "SELECT
            `op`.`products_name`, `op`.`products_price`, `op`.`products_quantity`, `op`.`products_model`,
            (`op`.`products_price` / `cur`.`value`) as `products_price_eur`
            FROM `orders_products` `op`
            JOIN `orders` `o` ON `o`.`orders_id` = `op`.`orders_id`
            JOIN `currencies` `cur` on `cur`.`code` = `o`.`currency`
            WHERE `op`.`orders_id` = :order_id
            LIMIT 5";
        $positionsStatement = $this->dbConnection->prepare($positionsQuery);
        $positionsStatement->bindValue('order_id', $orderId);
        $positionsRow = $positionsStatement->executeQuery()->fetchAll();
        
        foreach($positionsRow as $row) {
            $positions[] = [
                'positionDescription' => $row['products_name'],
                'count'               => (int)$row['products_quantity'],
                'weightInGrams'       => 0,
                'values'              => (float)$row['products_price'],
                'originCountry'       => '',
                'articleReference'    => $row['products_model'],
                'tarifNumber'         => '',
            ];
        }
        
        $orderData['customsDocument'] = [
            'currency'               => $orderRow['currency'],
            'originalShipmentNumber' => $shipmentNumber,
            'originalOperator'       => $operator,
            'acommpanyingDocument'   => '',
            'originalInvoiceNumber'  => $invoiceNumber,
            'originalInvoiceDate'    => $invoiceDate,
            'comment'                => '',
            'positions'              => $positions,
        ];
        
        return $orderData;
    }
}