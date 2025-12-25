<?php
/* --------------------------------------------------------------
   AbstractLocalizedContentAttributeCollection.inc.php 2022-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AbstractLocalizedContentAttributeCollection
 *
 * This class represents an abstract collection for localized attributes of a content entity
 *
 * @category   System
 * @package    Content
 */
abstract class AbstractLocalizedContentAttributeCollection extends AbstractCollection
    implements LocalizedContentAttributeCollectionInterface, JsonSerializable
{
    /**
     * Return an item by its language code
     *
     * @param LanguageCode $languageCode Language code
     *
     * @return LocalizedContentAttributeInterface
     *
     * @throws LocalizedAttributeNotFoundException If the item can not be found
     */
    public function itemByLanguageCode(LanguageCode $languageCode): LocalizedContentAttributeInterface
    {
        /**
         * @var LocalizedContentAttributeInterface $item
         */
        foreach ($this->getIterator() as $index => $item) {
            if ($item->languageCode() === $languageCode->asString()) {
                return $item;
            }
        }
        
        throw new LocalizedAttributeNotFoundException('Expected item of type ' . $this->_getValidType()
                                                      . ' has not been found');
    }
    
    
    /**
     * Return true if a language code exists at the collection
     *
     * @param LanguageCode $languageCode Language code
     *
     * @return bool
     */
    public function hasLanguageCode(LanguageCode $languageCode): bool
    {
        /**
         * @var LocalizedContentAttributeInterface $item
         */
        foreach ($this->getIterator() as $index => $item) {
            if ($item->languageCode() === $languageCode->asString()) {
                return true;
            }
        }
        
        return false;
    }
    
    
    /**
     * @return LocalizedContentAttributeInterface
     */
    public function getLastItem(): LocalizedContentAttributeInterface
    {
        $array = $this->getArray();
        
        return end($array);
    }
    
    
    /**
     * Specify data which should be serialized to JSON
     * @link  https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $result = new stdClass;
        
        /** @var LocalizedContentAttributeInterface $item */
        foreach ($this->getIterator() as $item) {
            
            $result->{$item->languageCode()} = $item;
        }
        
        return $result;
    }
}