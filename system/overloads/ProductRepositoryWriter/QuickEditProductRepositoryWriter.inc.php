<?php
/* --------------------------------------------------------------
   QuickEditProductRepositoryWriter.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class QuickEditProductRepositoryWriter
 */
class QuickEditProductRepositoryWriter extends QuickEditProductRepositoryWriter_parent
{
	/**
	 * Id of the product.
	 *
	 * @var int
	 */
	protected $productId;
	
	/**
	 * Model of the product.
	 *
	 * @var string
	 */
	protected $model;
	
	/**
	 * Quantity of the product.
	 *
	 * @var float
	 */
	protected $quantity;
	
	/**
	 * Price of the product.
	 *
	 * @var float
	 */
	protected $price;
	
	/**
	 * Discount of the product.
	 *
	 * @var float
	 */
	protected $discount;
	
	/**
	 * Tax of the product.
	 *
	 * @var float
	 */
	protected $tax;
	
	/**
	 * Tax rate of the product.
	 *
	 * @var float
	 */
	protected $taxRate;
	
	/**
	 * Shipping status name of the product.
	 *
	 * @var string
	 */
	protected $shippingStatusName;
	
	/**
	 * Weight of the product.
	 *
	 * @var float
	 */
	protected $weight;
	/**
	 * Shipping costs of the product.
	 *
	 * @var float
	 */
	protected $shippingCosts;
	
	/**
	 * Manufacturer of the product.
	 *
	 * @var string
	 */
	protected $manufacturer;
	
	/**
	 * Status of the product.
	 *
	 * @var int
	 */
	protected $status;
	
	/**
	 * Special price of the product.
	 *
	 * @var float
	 */
	protected $specialPrice;
	
	
	/**
	 * Stores the changes of the product.
	 *
	 * @param int   $productId Id of the product that should be updated.
	 * @param array $changes   An array containing the changes of the product.
	 *
	 * @return bool Returns true after the data has been successfully written - otherwise, false.
	 */
	public function updateProductByClause($productId, array $changes)
	{
		try
		{
			$this->productId          = $productId;
			$this->model              = null;
			$this->quantity           = null;
			$this->price              = null;
			$this->discount           = null;
			$this->tax                = null;
			$this->taxRate            = 0.0;
			$this->shippingStatusName = null;
			$this->weight             = null;
			$this->shippingCosts      = null;
			$this->manufacturer       = null;
			$this->status             = null;
			$this->specialPrice       = null;
            
            $product         = $this->db->get_where('products', ['products_id' => $this->productId])
                ->row_array();
			
			if(PRICE_IS_BRUTTO === 'true')
			{
				$this->_setTaxRate($product);
			}
			
			if(array_key_exists('price', $changes))
			{
				$this->_setPrice($changes['price']);
			}
			
			if(array_key_exists('name', $changes))
			{
				$this->db->update('products_description', ['products_name' => $changes['name']],
				                  ['products_id' => $this->productId, 'language_id' => $_SESSION['languages_id']]);
			}
			
			if(array_key_exists('model', $changes))
			{
				$this->_setModel($changes['model']);
			}
			
			if(array_key_exists('manufacturer', $changes))
			{
				$this->_setManufacturer($changes['manufacturer']);
			}
			
			if(array_key_exists('quantity', $changes))
			{
				$this->_setQuantity($changes['quantity']);
			}
			
			if(array_key_exists('tax', $changes))
			{
				$this->_setTax($changes['tax']);
				
				if(PRICE_IS_BRUTTO === 'true')
				{
					$productPrice    = (float)$product['products_price']; // Netto
					$oldTaxRate      = xtc_get_tax_rate($product['products_tax_class_id']) / 100 + 1;
					$newTaxRate      = xtc_get_tax_rate($changes['tax']) / 100 + 1;
					$newProductPrice = $productPrice * ($oldTaxRate / $newTaxRate);
					
					$this->db->update('products', ['products_price' => round((float)$newProductPrice, PRICE_PRECISION)],
					                  ['products_id' => $this->productId]);
				}
			}
			
			if(array_key_exists('discount', $changes))
			{
				$this->_setDiscount($changes['discount']);
			}
			
			if(array_key_exists('specialPrice', $changes))
			{
				$this->_setSpecialPrice($changes['specialPrice']);
				
				$result = $this->db->where('products_id', $this->productId)->get('specials')->num_rows();
				
				if(empty($result))
				{
					$this->db->insert('specials', [
						'products_id'                 => $this->productId,
						'specials_new_products_price' => $this->specialPrice,
						'expires_date'                => date('Y-m-d', strtotime('+7 days')),
						'status'                      => 0
					]);
				}
				else
				{
					$this->db->update('specials', [
						'specials_new_products_price' => $this->specialPrice
					], ['products_id' => $this->productId]);
				}
			}
			
			if(array_key_exists('shippingStatusName', $changes))
			{
				$this->_setShippingStatusName($changes['shippingStatusName']);
			}
			
			if(array_key_exists('weight', $changes))
			{
				$this->_setWeight($changes['weight']);
			}
			
			if(array_key_exists('shippingCosts', $changes))
			{
				$this->_setShippingCosts($changes['shippingCosts']);
			}
			
			if(array_key_exists('status', $changes))
			{
				$this->_setStatus($changes['status']);
			}
			
			$productsData = $this->_parseProductsData();
			
			$this->db->update('products', $productsData, ['products_id' => $this->productId]);
			
			return true;
		}
		catch(Exception $exception)
		{
			return false;
		}
	}
	
	
	/**
	 * Sets the model of the product.
	 *
	 * @param string $model Model of the product.
	 */
	protected function _setModel($model)
	{
		$this->model = $model;
	}
	
	
	/**
	 * Sets the manufacturer of the product.
	 *
	 * @param string $manufacturer Manufacturer of the product.
	 */
	protected function _setManufacturer($manufacturer)
	{
		$this->manufacturer = preg_replace('/\D/', '', $manufacturer);
	}
	
	
	/**
	 * Sets the quantity of the product.
	 *
	 * @param float $quantity Quantity of the product.
	 */
	protected function _setQuantity($quantity)
	{
		$this->quantity = $this->_calculateUnit($quantity, 'products_quantity');
	}
	
	
	/**
	 * Sets the price of the product.
	 *
	 * @param float $price Price of the product.
	 */
	protected function _setPrice($price)
	{
		$this->price = $this->_calculatePrice($price, 'products_price');
	}
	
	
	/**
	 * Sets the discount of the product.
	 *
	 * @param float $discount Discount of the product.
	 */
	protected function _setDiscount($discount)
	{
		$this->discount = $this->_calculateUnit($discount, 'products_discount_allowed');
	}
	
	
	/**
	 * Sets the specialPrice of the product.
	 *
	 * @param float $specialPrice SpecialPrice of the product.
	 */
	protected function _setSpecialPrice($specialPrice)
	{
		$this->specialPrice = $this->_calculatePrice($specialPrice, 'specials_new_products_price');
	}
	
	
	/**
	 * Sets the tax of the product.
	 *
	 * @param float $tax Tax of the product.
	 */
	protected function _setTax($tax)
	{
		$this->tax = $tax;
	}
	
	
	/**
	 * Sets the tax rate of the product.
	 */
	protected function _setTaxRate($product)
	{
		$this->taxRate = xtc_get_tax_rate($product['products_tax_class_id']);
	}
	
	
	/**
	 * Sets the shipping status name of the product.
	 *
	 * @param string $shippingStatusName Shipping status name of the product.
	 */
	protected function _setShippingStatusName($shippingStatusName)
	{
		$this->shippingStatusName = $shippingStatusName;
	}
	
	
	/**
	 * Sets the weight of the product.
	 *
	 * @param float $weight Weight of the product.
	 */
	protected function _setWeight($weight)
	{
		$this->weight = $this->_calculateUnit($weight, 'products_weight');
	}
	
	
	/**
	 * Sets the shipping costs of the product.
	 *
	 * @param float $shippingCosts Shipping costs of the product.
	 */
	protected function _setShippingCosts($shippingCosts)
	{
		$this->shippingCosts = $this->_calculateUnit($shippingCosts, 'nc_ultra_shipping_costs');
	}
	
	
	/**
	 * Sets the status of the product.
	 *
	 * @param int $status Status of the product.
	 */
	protected function _setStatus($status)
	{
		$this->status = $status;
	}
	
	
	/**
	 * Calculate the price of the value.
	 *
	 * @param string $value  Value.
	 * @param string $column Column of the table.
	 *
	 * @return float|int Price of the product.
	 */
	protected function _calculatePrice($value, $column)
	{
		$operation = preg_replace('/[^-+%]/', '', $value);
		$value     = preg_replace('/[^.,\d]/', '', $value);
		$value     = str_replace(',', '.', $value);
		$query     = $this->db->select($column)->where('products_id', $this->productId);
		
		if($column === 'specials_new_products_price')
		{
			$result = $query->get('specials')->row_array();
		}
		else
		{
			$result = $query->get('products')->row_array();
		}
		
		if(strpos($operation, '-') !== false)
		{
			return strpos($operation, '%') !== false ? $result[$column] * (1 - $value / 100) : $result[$column] - $value
			                                                                                                      / (1
			                                                                                                         + $this->taxRate
			                                                                                                           / 100);
		}
		
		if(strpos($operation, '+') !== false)
		{
			return strpos($operation, '%') !== false ? $result[$column] * (1 + $value / 100) : $result[$column] + $value
			                                                                                                      / (1
			                                                                                                         + $this->taxRate
			                                                                                                           / 100);
		}
		
		if(strpos($operation, '%') !== false)
		{
			if($column === 'specials_new_products_price')
			{
				if(empty($this->price))
				{
					$productsPrice = $query->select('products_price')
					                       ->where('products_id', $this->productId)
					                       ->get('products')
					                       ->row();
					
					return $productsPrice->products_price * (1 - $value / 100);
				}
				
				return $this->price * (1 - $value / 100);
			}
			
			return $result[$column] * (1 - $value / 100);
		}
		
		return $value / (1 + $this->taxRate / 100);
	}
	
	
	/**
	 * Calculates the unit of the value.
	 *
	 * @param string $value  Value.
	 * @param string $column Column of the table.
	 *
	 * @return mixed
	 */
	protected function _calculateUnit($value, $column)
	{
		$operation = preg_replace('/[^-+%]/', '', $value);
		$value     = preg_replace('/[^.,\d]/', '', $value);
		$value     = str_replace(',', '.', $value);
		
		$query  = $this->db->select($column)->where('products_id', $this->productId);
		$result = $query->get('products')->row_array();
		
		if(strpos($operation, '-') !== false)
		{
			return strpos($operation, '%') !== false ? $result[$column] * (1 - $value / 100) : $result[$column]
			                                                                                   - $value;
		}
		
		if(strpos($operation, '+') !== false)
		{
			return strpos($operation, '%') !== false ? $result[$column] * (1 + $value / 100) : $result[$column]
			                                                                                   + $value;
		}
		
		return $value;
	}
	
	
	/**
	 * Returns the allowed columns under consideration of filter criteria.
	 *
	 * @return array Allowed columns.
	 */
	protected function _parseProductsData()
	{
		$allowedColumns = [
			'products_model'            => $this->model,
			'products_quantity'         => $this->quantity,
			'products_price'            => $this->price,
			'products_discount_allowed' => $this->discount,
			'products_tax_class_id'     => $this->tax,
			'products_shippingtime'     => $this->shippingStatusName,
			'products_weight'           => $this->weight,
			'manufacturers_id'          => $this->manufacturer,
			'nc_ultra_shipping_costs'   => $this->shippingCosts,
			'products_status'           => $this->status
		];
		
		return array_filter($allowedColumns, function ($value) {
			return is_numeric($value) || is_string($value);
		});
	}
}