<?php
/* --------------------------------------------------------------
   QuantityUnit.inc.php 2017-08-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class GXEngineQuantityUnit
 *
 * @category   System
 * @package    QuantityUnit
 * @subpackage Entities
 */
class GXEngineQuantityUnit implements QuantityUnitInterface
{
    /**
     * @var int
     */
    protected $id = 0;
    
    /**
     * @var EditableKeyValueCollection
     */
    protected $names;
    
    
    /**
     * QuantityUnit constructor.
     *
     * @param \EditableKeyValueCollection $names
     */
    public function __construct(EditableKeyValueCollection $names)
    {
        $this->names = $names;
    }
    
    
    /**
     * Returns the id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    
    /**
     * Sets the id.
     *
     * @param \idType $quantityUnitId Id to be set.
     *
     * @return $this |\QuantityUnitInterface Same instance for chained method calls.
     */
    public function setId(idType $quantityUnitId)
    {
        $this->id = $quantityUnitId->asInt();
        
        return $this;
    }
    
    
    /**
     * Returns the quantity unit name.
     *
     * @param \LanguageCode $languageCode Language code for language specific name value.
     *
     * @return string Language specific name value.
     */
    public function getName(LanguageCode $languageCode)
    {
        $name = $this->names->getValue($languageCode->asString());
        
        if ($name !== '') {
            return $name;
        }
        
        foreach ($this->names as $langCode => $name) {
            if ($name !== '') {
                return "$name ($langCode)";
            }
        }
        
        return '-';
    }
    
    
    /**
     * Sets the quantity unit name.
     *
     * @param \StringType   $name         Name value to be set.
     * @param \LanguageCode $languageCode Language code for language specific name value.
     *
     * @return $this|\QuantityUnitInterface Same instance for chained method calls.
     */
    public function setName(StringType $name, LanguageCode $languageCode)
    {
        $this->names->setValue($languageCode->asString(), $name->asString());
        
        return $this;
    }
    
    
    /**
     * Returns all language specific names.
     *
     * @return array Language specific names value.
     */
    public function getNames()
    {
        return $this->names->getArray();
    }
}