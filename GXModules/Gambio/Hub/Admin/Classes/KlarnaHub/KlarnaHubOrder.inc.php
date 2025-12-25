<?php
/* --------------------------------------------------------------
   KlarnaHubOrder.inc.php 2023-04-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class KlarnaHubOrder
 *
 * Handles operations concerning Klarna Order data.
 *
 * @package    GXModules
 * @subpackage GambioHub
 */
class KlarnaHubOrder
{
	/**
	 * @var CI_DB_query_builder
	 */
	protected $queryBuilder;

	/**
	 * @var array
	 */
	protected $order;

	/**
	 * @var array
	 */
	protected $klarnaOrder;


	/**
	 * KlarnaHubOrder constructor.
	 *
	 * @param CI_DB_query_builder $queryBuilder Used for database operations.
	 * @param array               $order        Contains serialized shop order information.
	 * @param array               $klarnaOrder  Contains serialized Klarna order information.
	 */
	public function __construct(CI_DB_query_builder $queryBuilder, array $order, array $klarnaOrder)
	{
		$this->queryBuilder = $queryBuilder;
		$this->order        = $order;
		$this->klarnaOrder  = $klarnaOrder;
	}


	/**
	 * Returns the updated Klarna order lines based on the shop order state.
	 *
	 * @return array
	 */
	public function getUpdatedOrderLines()
	{
		$updatedOrderLines = [];

		foreach($this->klarnaOrder['order_lines'] as $klarnaOrderLine)
		{
			switch($klarnaOrderLine['reference'])
			{
				case 'ot_shipping':
				case 'ot_coupon':
				case 'ot_gv':
				case 'ot_discount':
					$updatedOrderLine = $this->_getUpdatedTotalOrderLine($klarnaOrderLine);
					break;
				default:
					$updatedOrderLine = $this->_getUpdatedProductOrderLine($klarnaOrderLine);
			}

			if($klarnaOrderLine['type'] === 'surcharge')
			{
			    $updatedOrderLine = $this->_getUpdatedSurchargeOrderLine($klarnaOrderLine);
            }

            if($klarnaOrderLine['reference'] === 'ot_gv')
            {
                $updatedOrderLine['totalAmount'] = -1 * $updatedOrderLine['totalAmount'];
                $updatedOrderLine['unitPrice'] = -1 * $updatedOrderLine['unitPrice'];
            }

			if(!empty($updatedOrderLine))
			{
				$updatedOrderLines[] = $updatedOrderLine;
			}
		}

		return $updatedOrderLines;
	}


	/**
	 * Returns the updated order totals line.
	 *
	 * @param array $klarnaOrderLine Contains serialized Klarna order line data.
	 *
	 * @return array
	 */
	protected function _getUpdatedTotalOrderLine(array $klarnaOrderLine)
	{
		foreach($this->order['totals'] as $shopOrderLine)
		{
			if($shopOrderLine['class'] !== $klarnaOrderLine['reference'])
			{
				continue;
			}

			return [
				'reference'           => $klarnaOrderLine['reference'],
				'type'                => $klarnaOrderLine['type'],
				'quantity'            => $klarnaOrderLine['quantity'],
				'quantityUnit'        => $klarnaOrderLine['quantity_unit'],
				'name'                => $klarnaOrderLine['name'],
				'totalAmount'         => KlarnaHubPrice::sanitize((float)$klarnaOrderLine['quantity']
				                                                  * (float)$shopOrderLine['value'] * 100),
				'unitPrice'           => KlarnaHubPrice::sanitize((float)$shopOrderLine['value'] * 100),
				'totalDiscountAmount' => $klarnaOrderLine['total_discount_amount'],
				'taxRate'             => $klarnaOrderLine['tax_rate'],
				'totalTaxAmount'      => $klarnaOrderLine['total_tax_amount'],
				'merchantData'        => $klarnaOrderLine['merchant_data'],
				'productUrl'          => $klarnaOrderLine['product_url'],
				'imageUrl'            => $klarnaOrderLine['image_url'] ?? null,
				'productIdentifiers'  => [
					'categoryPath'           => $klarnaOrderLine['product_identifiers']['category_path'],
					'globalTradeItemNumber'  => $klarnaOrderLine['product_identifiers']['global_trade_item_number'],
					'manufacturerPartNumber' => $klarnaOrderLine['product_identifiers']['manufacturer_part_number'],
					'brand'                  => $klarnaOrderLine['product_identifiers']['brand']
				]
			];
		}

		return [];
	}

    /**
     * Returns the surchage order line.
     *
     * @param array $klarnaOrderLine Contains serialized Klarna order line data.
     *
     * @return array
     */
    protected function _getUpdatedSurchargeOrderLine(array $klarnaOrderLine)
    {
        foreach($this->order['totals'] as $shopOrderLine) {
            if ($shopOrderLine['class'] !== $klarnaOrderLine['reference']) {
                continue;
            }

            return [
                'reference'           => $klarnaOrderLine['reference'],
                'type'                => $klarnaOrderLine['type'],
                'quantity'            => $klarnaOrderLine['quantity'],
                'quantityUnit'        => $klarnaOrderLine['quantity_unit'],
                'name'                => $klarnaOrderLine['name'],
                'totalAmount'         => KlarnaHubPrice::sanitize((float)$klarnaOrderLine['quantity']
                                                                  * (float)$shopOrderLine['value'] * 100),
                'unitPrice'           => KlarnaHubPrice::sanitize((float)$shopOrderLine['value'] * 100),
                'totalDiscountAmount' => $klarnaOrderLine['total_discount_amount'],
                'taxRate'             => $klarnaOrderLine['tax_rate'],
                'totalTaxAmount'      => $klarnaOrderLine['total_tax_amount'],
                'merchantData'        => $klarnaOrderLine['merchant_data'],
                'productUrl'          => $klarnaOrderLine['product_url'],
                'imageUrl'            => $klarnaOrderLine['image_url'] ?? null,
                'productIdentifiers'  => [
                    'categoryPath'           => $klarnaOrderLine['product_identifiers']['category_path'],
                    'globalTradeItemNumber'  => $klarnaOrderLine['product_identifiers']['global_trade_item_number'],
                    'manufacturerPartNumber' => $klarnaOrderLine['product_identifiers']['manufacturer_part_number'],
                    'brand'                  => $klarnaOrderLine['product_identifiers']['brand']
                ]
            ];
        }
    }


	/**
	 * Returns the updated order product line.
	 *
	 * @param array $klarnaOrderLine Contains serialized Klarna order line data.
	 *
	 * @return array
	 */
	protected function _getUpdatedProductOrderLine(array $klarnaOrderLine)
	{
		$shopOrderLine = null;

		// Product properties/attributes
		if(preg_match('/\d+\D\d+/', $klarnaOrderLine['reference'])
		   || preg_match('/\d+{\d+}\d+/', $klarnaOrderLine['reference']))
		{
			foreach($this->order['items'] as $item)
			{
                if($item['addonValues']['identifier'] === $klarnaOrderLine['reference']
                    || $item['addonValues']['identifier'] === $klarnaOrderLine['merchant_data'])
				{
					$shopOrderLine = $item;
					break;
				}
			}
		}

		// Product without properties or attributes.
		if(preg_match('/^\d+$/', $klarnaOrderLine['reference']))
		{
			foreach($this->order['items'] as $item)
			{
				if((int)$item['addonValues']['productId'] === (int)$klarnaOrderLine['reference'])
				{
					$shopOrderLine = $item;
					break;
				}
			}
		}

		// Try finding an order based on the product model (fallback solution).
		if($shopOrderLine === null)
		{
			foreach($this->order['items'] as $item)
			{
				// Trim required, due to issues with older Hub Connector versions when the product has properties (used
				// to result to a Klarna order line reference similar to "-{product-model-value}").
				if($item['model'] === trim($klarnaOrderLine['reference'], '-'))
				{
					$shopOrderLine = $item;
					break;
				}
			}
		}

		if($shopOrderLine === null)
		{
			return []; // No shop order line matched the Klarna order.
		}

		// Workaround for floating point quantity values (they aren't accepted by Klarna).
		if(abs($shopOrderLine['quantity'] - round($shopOrderLine['quantity'])) > 0)
		{
			$shopOrderLine['name']     = $shopOrderLine['quantity'] . ' ' . $shopOrderLine['name'];
			$shopOrderLine['quantity'] = 1;
			$shopOrderLine['price']    = $shopOrderLine['finalPrice'];
		}

		return [
			'reference'           => $klarnaOrderLine['reference'],
			'type'                => $klarnaOrderLine['type'],
			'quantity'            => $shopOrderLine['quantity'],
			'quantityUnit'        => $shopOrderLine['quantityUnitName'],
			'name'                => $shopOrderLine['name'] ? $shopOrderLine['name'] : 'unnamed product',
			'totalAmount'         => KlarnaHubPrice::sanitize((float)$shopOrderLine['finalPrice'] * 100),
			'unitPrice'           => KlarnaHubPrice::sanitize((float)$shopOrderLine['price'] * 100),
			'totalDiscountAmount' => $klarnaOrderLine['total_discount_amount'],
			'taxRate'             => KlarnaHubPrice::sanitize((float)$shopOrderLine['tax'] * 100),
			'totalTaxAmount'      => $klarnaOrderLine['total_tax_amount'],
			'merchantData'        => $klarnaOrderLine['merchant_data'],
			'productUrl'          => $klarnaOrderLine['product_url'],
			'imageUrl'            => $klarnaOrderLine['image_url'] ?? null,
			'productIdentifiers'  => [
				'categoryPath'           => $klarnaOrderLine['product_identifiers']['category_path'],
				'globalTradeItemNumber'  => $klarnaOrderLine['product_identifiers']['global_trade_item_number'],
				'manufacturerPartNumber' => $klarnaOrderLine['product_identifiers']['manufacturer_part_number'],
				'brand'                  => $klarnaOrderLine['product_identifiers']['brand']
			]
		];
	}
}
