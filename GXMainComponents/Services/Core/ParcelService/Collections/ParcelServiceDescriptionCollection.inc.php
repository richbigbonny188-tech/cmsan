<?php
/* --------------------------------------------------------------
   ParcelServiceDescriptionCollection.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ParcelServiceDescriptionCollection
 */
class ParcelServiceDescriptionCollection implements \Countable, \IteratorAggregate
{
    /**
     * @var array
     */
    protected $parcelServiceDescriptions = [];
    
    
    /**
     * ParcelServiceDescriptionCollection constructor.
     *
     * @param array $parcelServiceDescriptions
     */
    public function __construct(array $parcelServiceDescriptions)
    {
        foreach ($parcelServiceDescriptions as $parcelServiceDescription) {
            $this->add($parcelServiceDescription);
        }
    }
    
    
    /**
     * Returns a ParcelServiceDescriptionCollection containing given parcel service descriptions.
     *
     * @param array $parcelServiceDescriptions
     *
     * @return bool|\ParcelServiceDescriptionCollection
     */
    public static function collect(array $parcelServiceDescriptions)
    {
        return MainFactory::create(static::class, $parcelServiceDescriptions);
    }
    
    
    /**
     * Returns the collection as an array.
     *
     * @return array
     */
    public function getArray()
    {
        return $this->parcelServiceDescriptions;
    }
    
    
    /**
     * @return \ArrayIterator|\Traversable
     */
    public function getIterator(): \Traversable
    {
        return new ArrayIterator($this->parcelServiceDescriptions);
    }
    
    
    /**
     * Returns the count of parcel service descriptions in the collection.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->parcelServiceDescriptions);
    }
    
    
    /**
     * Adds a parcel service description to the collection.
     *
     * @param \ParcelServiceDescriptionInterface $parcelServiceDescription
     */
    protected function add(ParcelServiceDescriptionInterface $parcelServiceDescription)
    {
        $this->parcelServiceDescriptions[] = $parcelServiceDescription;
    }
}
