<?php

/* --------------------------------------------------------------
   VPE.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class VPE
 *
 * @category   System
 * @package    VPE
 * @subpackage Entities
 */
class VPE implements VPEInterface
{
    /**
     * @var int
     */
    protected $id = 0;
    
    /**
     * @var \KeyValueCollection
     */
    protected $names;
    
    /**
     * @var bool
     */
    protected $default = false;
    
    
    /**
     * VPE constructor.
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
     * @param \IdType $vpeId Id to be set.
     *
     * @return $this|\VPEInterface Same instance for chained method calls.
     */
    public function setId(IdType $vpeId)
    {
        $this->id = $vpeId->asInt();
        
        return $this;
    }
    
    
    /**
     * Returns the language specific name.
     *
     * @param \LanguageCode $languageCode Language code for language specific name value.
     *
     * @return string Language specific name value.
     */
    public function getName(LanguageCode $languageCode)
    {
        if ($this->names->keyExists($languageCode->asString())) {
            $name = $this->names->getValue($languageCode->asString());
            
            if ($name !== '') {
                return $name;
            }
        }
        
        foreach ($this->names as $langCode => $name) {
            if ($name !== '') {
                return "$name ($langCode)";
            }
        }
        
        return '-';
    }
    
    
    /**
     * Sets the language specific name.
     *
     * @param \StringType   $name         Name value to be set.
     * @param \LanguageCode $languageCode Language code for language specific name value.
     *
     * @return $this|\VPEInterface Same instance for chained method calls.
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
    
    
    /**
     * Returns true if the vpe entity instance is the shops default.
     *
     * @return bool True if VPE value is shops default.
     */
    public function isDefault()
    {
        return $this->default;
    }
    
    
    /**
     * Sets vpe entity to shops default, if bool type is true.
     *
     * @param \BoolType $default Is current vpe entity shops default?
     *
     * @return $this|\VPEInterface Same instance for chained method calls.
     */
    public function setDefault(BoolType $default)
    {
        $this->default = $default->asBool();
        
        return $this;
    }
}
