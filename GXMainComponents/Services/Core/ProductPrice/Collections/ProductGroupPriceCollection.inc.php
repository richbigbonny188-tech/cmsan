<?php
/* --------------------------------------------------------------
   ProductGroupPriceCollection.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductGroupPriceCollection
 *
 * @category   System
 * @package    ProductPrice
 * @subpackage Entities
 */
class ProductGroupPriceCollection implements \IteratorAggregate, \Countable
{
    protected $groupPrices = [];
    
    
    public function __construct(array $productGroupPrices)
    {
        foreach ($productGroupPrices as $groupPrice) {
            $this->_add($groupPrice);
        }
    }
    
    
    public static function collect(array $productGroupPrices)
    {
        return MainFactory::create(static::class, $productGroupPrices);
    }
    
    
    public function getArray()
    {
        return $this->groupPrices;
    }
    
    
    public function getIterator(): \Traversable
    {
        return new ArrayIterator($this->groupPrices);
    }
    
    
    public function count(): int
    {
        return count($this->groupPrices);
    }
    
    
    protected function _add(ProductGroupPriceInterface $groupPrice)
    {
        $this->groupPrices[] = $groupPrice;
    }
}