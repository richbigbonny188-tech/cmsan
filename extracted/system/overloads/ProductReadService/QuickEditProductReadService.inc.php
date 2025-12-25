<?php

/* --------------------------------------------------------------
   QuickEditProductReadService.inc.php 2017-03-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class QuickEditProductReadService
 *
 * @category   System
 * @package    QuickEdit
 * @subpackage Overloads
 */
class QuickEditProductReadService extends QuickEditProductReadService_parent
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
		return $this->productRepo->between($this->start, $this->length)
		                         ->orderBy($this->orderBy)
		                         ->getFilteredProducts($filterParameters);
	}
	
	
	/**
	 * Returns the number of products found under consideration of filter criteria.
	 *
	 * @param array $filterParameters Filter parameters.
	 *
	 * @return array Returns an array of QuickEditProductListItem or an empty array.
	 */
	public function getFilteredProductsCount(array $filterParameters)
	{
		return $this->productRepo->getFilteredProductsCount($filterParameters);
	}
	
	
	/**
	 * Specifies the value for the start point and its number.
	 *
	 * @param IntType|null $start  The point from which data record is to be searched in the product table.
	 * @param IntType|null $length The number of products to return.
	 *
	 * @return QuickEditProductReadService Returns same instance for chained method calls.
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
	 * @return QuickEditProductReadService Returns same instance for chained method calls.
	 */
	public function orderBy(StringType $orderBy = null)
	{
		$this->orderBy = $orderBy;
		
		return $this;
	}
}