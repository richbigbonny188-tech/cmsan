<?php
/* --------------------------------------------------------------
   GraduatedPriceCollection.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class GraduatedPriceCollection
 *
 * @category   System
 * @package    ProductPrice
 * @subpackage Entities
 */
class GraduatedPriceCollection implements \IteratorAggregate, \Countable
{
    protected $graduatedPrice = [];
    
    
    public function __construct(array $graduatedPrices)
    {
        foreach ($graduatedPrices as $graduatedPrice) {
            $this->_add($graduatedPrice);
        }
    }
    
    
    public static function collect(array $graduatedPrices)
    {
        return MainFactory::create(static::class, $graduatedPrices);
    }
    
    
    public function getArray()
    {
        return $this->graduatedPrice;
    }
    
    
    public function getIterator(): \Traversable
    {
        return new ArrayIterator($this->graduatedPrice);
    }
    
    
    public function count(): int
    {
        return count($this->graduatedPrice);
    }
    
    
    protected function _add(GraduatedPriceInterface $graduatedPrice)
    {
        $this->graduatedPrice[] = $graduatedPrice;
    }
}