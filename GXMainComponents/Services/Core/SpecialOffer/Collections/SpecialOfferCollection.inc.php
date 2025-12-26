<?php
/* --------------------------------------------------------------
   SpecialOfferCollection.inc.php 2022-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SpecialOfferCollection
 */
class SpecialOfferCollection implements \Countable, \IteratorAggregate
{
    /**
     * @var \SpecialOfferInterface[]
     */
    protected $specialOffers = [];
    
    
    /**
     * SpecialOfferCollection constructor.
     *
     * @param \SpecialOfferInterface[] $specialOffers Special offer entities to be collected.
     */
    public function __construct(array $specialOffers)
    {
        foreach ($specialOffers as $specialOffer) {
            $this->add($specialOffer);
        }
    }
    
    
    /**
     * Named constructor of special offer collection.
     *
     * @param \SpecialOfferInterface[] $specialOffers Special offer entities to be collected.
     *
     * @return \SpecialOfferCollection New instance.
     */
    public static function collect(array $specialOffers)
    {
        return MainFactory::create(static::class, $specialOffers);
    }
    
    
    /**
     * Returns the collection items.
     *
     * @return \SpecialOfferInterface[]
     */
    public function getArray()
    {
        return $this->specialOffers;
    }
    
    
    /**
     * Adds a new item to the collection.
     *
     * @param \SpecialOfferInterface $specialOffer Special offer to be added.
     */
    protected function add(SpecialOfferInterface $specialOffer)
    {
        $this->specialOffers[] = $specialOffer;
    }
    
    
    /**
     * Returns the collection items as array iterator to loop through the collection.
     *
     * @return \ArrayIterator|\Traversable
     */
    public function getIterator(): \Traversable
    {
        return new ArrayIterator($this->specialOffers);
    }
    
    
    /**
     * Returns the count of all collection items.
     *
     * @return int Count of all collection items.
     */
    public function count(): int
    {
        return count($this->specialOffers);
    }
}
