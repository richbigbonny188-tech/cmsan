<?php
/* --------------------------------------------------------------
   QuickEditProductRepositoryReader.inc.php 2020-06-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class QuickEditProductRepositoryReader
 */
class QuickEditProductRepositoryReader extends QuickEditProductRepositoryReader_parent
{
	/**
	 * Returns products that are subject to the specified filter criteria.
	 *
	 * @param array $filterParameters Filter parameters.
	 *
	 * @return array Returns the query result as a pure array, or an empty array when no result is produced.
	 */
	public function getFilteredProducts(array $filterParameters)
	{
		$this->_filter($filterParameters);
		$this->_join();
		
		$result = $this->db->select($this->_columns())->group_by('products.products_id')->from('products');
		
		return $result->get()->result_array();
	}
	
	
	/**
	 * Returns the number of products found.
	 *
	 * @param array $filterParameters Filter parameters.
	 *
	 * @return int Returns the number of products found.
	 */
	public function getFilteredProductsCount(array $filterParameters)
	{
	    $this->_filter($filterParameters);
	    $this->_join();
	    
	    $this->db->select('products.products_id');
	    $this->db->distinct();
	    
	    return $this->db->count_all_results('products', false);
	}
	
	
	/**
	 * Specifies the value for the start point and its number.
	 *
	 * @param IntType|null $start  The point from which data record is to be searched in the product table.
	 * @param IntType|null $length The number of products to return.
	 *
	 * @return QuickEditProductReadService Returns the actual instance of QuickEditProductRepositoryReader.
	 */
	public function between(IntType $start = null, IntType $length = null)
	{
		$this->db->limit($length->asInt(), $start->asInt());
		
		return $this;
	}
	
	
	/**
	 * Sets the sort order.
	 *
	 * @param StringType|null $orderBy Sort order.
	 *
	 * @return QuickEditProductReadService Returns the actual instance of QuickEditProductReadService.
	 */
	public function orderBy(StringType $orderBy)
	{
		$this->db->order_by($orderBy->asString());
		$this->db->order_by('products.products_id', 'ASC');
		
		return $this;
	}
	
	
	/**
	 * Builds the query to the database, taking into account the filter criteria.
	 *
	 * @param array $filterParameters Filter parameters.
	 *
	 * @return QuickEditProductReadService Returns the actual instance of QuickEditProductReadService.
	 */
	protected function _filter(array $filterParameters)
	{
		$filter = [];
		$brutto = '';
		
		if(PRICE_IS_BRUTTO === 'true')
		{
			$brutto = ' / (1 + (`tax_rates`.`tax_rate` / 100))';
		}
		
		foreach($filterParameters as $parameter => $value)
		{
			$value              = preg_replace('/[^\w\s+<>*.,"\'-]/u', '', $value);
			$filter[$parameter] = str_replace('*', '%', $value);
		}
		
		if(array_key_exists('id', $filter))
		{
			$this->db->where('products.products_id', $filter['id']);
		}
		
		if(array_key_exists('name', $filter))
		{
			$this->db->where('products_description.products_name LIKE ', $filter['name']);
		}
		
		if(array_key_exists('manufacturer', $filter))
		{
			$this->db->group_start()->where('products.manufacturers_id', array_shift($filter['manufacturer']));
			
			foreach($filter['manufacturer'] as $value)
			{
				$this->db->or_where('products.manufacturers_id', $value);
			}
			
			$this->db->group_end();
		}
		
		if(array_key_exists('category', $filter))
		{
			if(is_array($filter['category']))
			{
				$this->db->group_start()
				         ->where('categories_description.categories_id', array_shift($filter['category']));
				
				foreach($filter['category'] as $value)
				{
					$this->db->or_where('categories_description.categories_id', $value);
				}
				
				$this->db->group_end();
			}
			else
			{
				$this->db->where('categories_description.categories_name LIKE ', $filter['category']);
			}
		}
		
		if(array_key_exists('model', $filter))
		{
			$this->db->where('products.products_model LIKE ', $filter['model']);
		}
		
		if(array_key_exists('quantity', $filter))
		{
			if(is_array($filter['quantity']))
			{
				$this->db->group_start()->where('products.products_quantity >=', array_shift($filter['quantity']));
				
				foreach($filter['quantity'] as $value)
				{
					$this->db->where('products.products_quantity <=', $value);
				}
				
				$this->db->group_end();
			}
			else
			{
				if(strpos($filter['quantity'], '<') === 0 || strpos($filter['quantity'], '>') >= 1)
				{
					$this->db->where('products.products_quantity < ', str_replace(['<', '>'], '', $filter['quantity']));
				}
				elseif(strpos($filter['quantity'], '>') === 0 || strpos($filter['quantity'], '<') >= 1)
				{
					$this->db->where('products.products_quantity > ', str_replace(['<', '>'], '', $filter['quantity']));
				}
				else
				{
					$this->db->where('products.products_quantity = ', $filter['quantity']);
				}
			}
		}
		
		// TODO: nur zahlen erlauben
		if(array_key_exists('price', $filter))
		{
			if(is_array($filter['price']))
			{
				$this->db->group_start()->where('products.products_price >= ROUND(' . array_shift($filter['price'])
				                                . $brutto . ', 4)');
				
				foreach($filter['price'] as $value)
				{
					$this->db->where('products.products_price <= ROUND(' . $value . $brutto . ', 4)');
				}
				
				$this->db->group_end();
			}
			else
			{
				if(strpos($filter['price'], '<') === 0 || strpos($filter['price'], '>') >= 1)
				{
					$this->db->where('products.products_price < ROUND(' . str_replace(['<', '>'], '', $filter['price'])
					                 . $brutto . ', 4)');
				}
				elseif(strpos($filter['price'], '>') === 0 || strpos($filter['price'], '<') >= 1)
				{
					$this->db->where('products.products_price > ROUND(' . str_replace(['<', '>'], '', $filter['price'])
					                 . $brutto . ', 4)');
				}
				else
				{
					$this->db->where('products.products_price = ROUND(' . $filter['price'] . $brutto . ', 4)');
				}
			}
		}
		
		if(array_key_exists('discount', $filter))
		{
			if(is_array($filter['discount']))
			{
				$this->db->group_start()->where('products.products_discount_allowed >= ROUND('
				                                . array_shift($filter['discount']) . $brutto . ', 4)');
				
				foreach($filter['discount'] as $value)
				{
					$this->db->where('products.products_discount_allowed <= ROUND(' . $value . $brutto . ', 4)');
				}
				
				$this->db->group_end();
			}
			else
			{
				if(strpos($filter['discount'], '<') === 0 || strpos($filter['discount'], '>') >= 1)
				{
					$this->db->where('products.products_discount_allowed < ROUND(' . str_replace(['<', '>'], '',
					                                                                             $filter['discount'])
					                 . $brutto . ', 4)');
				}
				elseif(strpos($filter['discount'], '>') === 0 || strpos($filter['discount'], '<') >= 1)
				{
					$this->db->where('products.products_discount_allowed > ROUND(' . str_replace(['<', '>'], '',
					                                                                             $filter['discount'])
					                 . $brutto . ', 4)');
				}
				else
				{
					$this->db->where('products.products_discount_allowed = ROUND(' . $filter['discount'] . $brutto
					                 . ', 4)');
				}
			}
		}
		
		if(array_key_exists('specialPrice', $filter))
		{
			if(is_array($filter['specialPrice']))
			{
				$this->db->group_start()->where('specials.specials_new_products_price >= ROUND('
				                                . array_shift($filter['specialPrice']) . $brutto . ', 4)');
				
				foreach($filter['specialPrice'] as $value)
				{
					$this->db->where('specials.specials_new_products_price <= ROUND(' . $value . $brutto . ', 4)');
				}
				
				$this->db->group_end();
			}
			else
			{
				if(strpos($filter['specialPrice'], '<') === 0 || strpos($filter['specialPrice'], '>') >= 1)
				{
					$this->db->where('specials.specials_new_products_price < ROUND(' . str_replace(['<', '>'], '',
					                                                                               $filter['specialPrice'])
					                 . $brutto . ', 4)');
				}
				elseif(strpos($filter['specialPrice'], '>') === 0 || strpos($filter['specialPrice'], '<') >= 1)
				{
					$this->db->where('specials.specials_new_products_price > ROUND(' . str_replace(['<', '>'], '',
					                                                                               $filter['specialPrice'])
					                 . $brutto . ', 4)');
				}
				else
				{
					$this->db->where('specials.specials_new_products_price = ROUND(' . $filter['specialPrice'] . $brutto
					                 . ', 4)');
				}
			}
		}
		
		if(array_key_exists('tax', $filter))
		{
			$this->db->group_start()->where('products.products_tax_class_id', array_shift($filter['tax']));
			
			foreach($filter['tax'] as $value)
			{
				$this->db->or_where('products.products_tax_class_id', $value);
			}
			
			$this->db->group_end();
		}
		
		if(array_key_exists('status', $filter))
		{
			$this->db->group_start()->where('products.products_status', array_shift($filter['status']));
			
			foreach($filter['status'] as $value)
			{
				$this->db->or_where('products.products_status', $value);
			}
			
			$this->db->group_end();
		}
		
		if(array_key_exists('shippingStatusName', $filter))
		{
			$this->db->group_start()
			         ->where('products.products_shippingtime', array_shift($filter['shippingStatusName']));
			
			foreach($filter['shippingStatusName'] as $value)
			{
				$this->db->or_where('products.products_shippingtime', $value);
			}
			
			$this->db->group_end();
		}
		
		if(array_key_exists('weight', $filter))
		{
			if(is_array($filter['weight']))
			{
				$this->db->group_start()->where('products.products_weight >=', array_shift($filter['weight']));
				
				foreach($filter['weight'] as $value)
				{
					$this->db->where('products.products_weight <=', $value);
				}
				
				$this->db->group_end();
			}
			else
			{
				if(strpos($filter['weight'], '<') === 0 || strpos($filter['weight'], '>') >= 1)
				{
					$this->db->where('products.products_weight < ', str_replace(['<', '>'], '', $filter['weight']));
				}
				elseif(strpos($filter['weight'], '>') === 0 || strpos($filter['weight'], '<') >= 1)
				{
					$this->db->where('products.products_weight > ', str_replace(['<', '>'], '', $filter['weight']));
				}
				else
				{
					$this->db->where('products.products_weight = ', $filter['weight']);
				}
			}
		}
		
		if(array_key_exists('shippingCosts', $filter))
		{
			if(is_array($filter['shippingCosts']))
			{
				$this->db->group_start()
				         ->where('products.nc_ultra_shipping_costs >=', array_shift($filter['shippingCosts']));
				
				foreach($filter['shippingCosts'] as $value)
				{
					$this->db->where('products.nc_ultra_shipping_costs <=', $value);
				}
				
				$this->db->group_end();
			}
			else
			{
				if(strpos($filter['shippingCosts'], '<') === 0 || strpos($filter['shippingCosts'], '>') >= 1)
				{
					$this->db->where('products.nc_ultra_shipping_costs < ',
					                 str_replace(['<', '>'], '', $filter['shippingCosts']));
				}
				elseif(strpos($filter['shippingCosts'], '>') === 0 || strpos($filter['shippingCosts'], '<') >= 1)
				{
					$this->db->where('products.nc_ultra_shipping_costs > ',
					                 str_replace(['<', '>'], '', $filter['shippingCosts']));
				}
				else
				{
					$this->db->where('products.nc_ultra_shipping_costs = ', $filter['shippingCosts']);
				}
			}
		}
		
		return $this;
	}
	
	
	/**
	 * Sets the required joins for the database query.
	 */
	protected function _join()
	{
		$this->db->join('languages', 'languages.languages_id = ' . $_SESSION['languages_id']);
		$this->db->join('zones_to_geo_zones', 'zones_to_geo_zones.zone_country_id = ' . (int)STORE_COUNTRY);
		$this->db->join('tax_rates',
		                'tax_rates.tax_class_id = products.products_tax_class_id AND tax_rates.tax_zone_id = zones_to_geo_zones.geo_zone_id',
		                'left');
		$this->db->join('products_description', 'products_description.products_id = products.products_id');
		$this->db->join('products_to_categories', 'products_to_categories.products_id = products.products_id');
		$this->db->join('categories_description',
		                'categories_description.categories_id = products_to_categories.categories_id AND categories_description.language_id = languages.languages_id',
		                'left');
		$this->db->join('specials', 'specials.products_id = products.products_id', 'left');
		$this->db->join('shipping_status',
		                'shipping_status.shipping_status_id = products.products_shippingtime AND shipping_status.language_id = languages.languages_id',
		                'left');
		$this->db->join('manufacturers', 'manufacturers.manufacturers_id = products.manufacturers_id', 'left');
		
		$this->db->where('products_description.language_id = languages.languages_id');
	}
	
	
	/**
	 * Returns the required columns of the database.
	 *
	 * @return array Returns the required columns of the database.
	 */
	protected function _columns()
	{
		return [
			'products.products_id',
			'products.products_model',
			'products.products_quantity',
			'products.products_price',
			'products.products_discount_allowed',
			'products.products_weight',
			'products.products_tax_class_id',
			'products.products_shippingtime',
			'products.nc_ultra_shipping_costs',
			'products.products_status',
			'products_description.products_name',
			'categories_description.categories_id',
			'manufacturers.manufacturers_name as products_manufacturer',
			'GROUP_CONCAT(DISTINCT categories_description.categories_name SEPARATOR ", ") as products_categories',
			'shipping_status.shipping_status_name',
			'specials.specials_id',
			'specials.specials_new_products_price',
			'specials.expires_date',
			'specials.specials_quantity',
			'specials.status',
			'MAX(tax_rates.tax_rate) as tax_rate'
		];
	}
}