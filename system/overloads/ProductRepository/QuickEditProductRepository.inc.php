<?php

/* --------------------------------------------------------------
   QuickEditProductRepository.inc.php 2018-04-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class QuickEditProductRepository
 *
 * @category   System
 * @package    QuickEdit
 * @subpackage Overloads
 */
class QuickEditProductRepository extends QuickEditProductRepository_parent
{
	/**
	 * The point from which data record is to be searched in the product table.
	 *
	 * @var IntType|null
	 */
	protected $start;
	
	/**
	 * The number of products to return.
	 *
	 * @var IntType|null
	 */
	protected $length;
	
	/**
	 * Sort order.
	 *
	 * @var StringType|null
	 */
	protected $orderBy;
	
	
	/**
	 * Returns products that are subject to the specified filter criteria.
	 *
	 * @param array $filterParameters Filter parameters.
	 *
	 * @return array Returns an array of QuickEditProductListItem or an empty array.
	 */
	
	public function getFilteredProducts(array $filterParameters)
	{
		$products = $this->reader->between($this->start, $this->length)
		                         ->orderBy($this->orderBy)
		                         ->getFilteredProducts($filterParameters);
		
		return $this->_collectionContentArray($products);
	}
	
	
	/**
	 * Returns the number of products found under consideration of filter criteria.
	 *
	 * @param array $filterParameters Filter parameters.
	 *
	 * @return int Returns an array of QuickEditProductListItem or an empty array.
	 */
	public function getFilteredProductsCount(array $filterParameters)
	{
		return $this->reader->getFilteredProductsCount($filterParameters);
	}
	
	
	/**
	 * Stores the changes of the product.
	 *
	 * @param int $productId Id of the product that should be updated.
	 * @param array $changes An array containing the changes of the product.
	 *
	 * @return bool Returns true after the data has been successfully written - otherwise, false.
	 */
	public function updateProductByClause($productId, array $changes)
	{
		return $this->writer->updateProductByClause($productId, $changes);
	}
	
	
	/**
	 * Specifies the value for the start point and its number.
	 *
	 * @param IntType|null $start  The point from which data record is to be searched in the product table.
	 * @param IntType|null $length The number of products to return.
	 *
	 * @return QuickEditProductRepository Returns same instance for chained method calls.
	 */
	public function between(IntType $start = null, IntType $length = null)
	{
		$this->start  = $start;
		$this->length = $length;
		
		return $this;
	}
	
	
	/**
	 * Sets the sort order.
	 *
	 * @param StringType|null $orderBy Sort order.
	 *
	 * @return QuickEditProductRepository Returns same instance for chained method calls.
	 */
	public function orderBy(StringType $orderBy)
	{
		$this->orderBy = $orderBy;
		
		return $this;
	}
	
	
	/**
	 * Returns an array of QuickEditProductListItem.
	 *
	 * @param array $products An array containing the data required for a product.
	 *
	 * @return array Returns an array of QuickEditProductListItem or an empty array.
	 */
	protected function _collectionContentArray(array $products)
	{
		$collection = [];
		foreach($products as $value)
		{
			$collection[] = MainFactory::create('QuickEditProductListItem', $value);
		}
		
		return $collection;
	}
}