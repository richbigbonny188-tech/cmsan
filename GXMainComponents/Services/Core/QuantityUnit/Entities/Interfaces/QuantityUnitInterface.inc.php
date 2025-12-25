<?php

/* --------------------------------------------------------------
   QuantityUnitInterface.inc.php 2017-08-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface QuantityUnitInterface
 *
 * @category   System
 * @package    QuantityUnit
 * @subpackage Entities
 */
interface QuantityUnitInterface
{
    /**
     * Returns the id.
     *
     * @return int
     */
    public function getId();
    
    
    /**
     * Sets the id.
     *
     * @param \idType $quantityUnitId Id to be set.
     *
     * @return $this|\QuantityUnitInterface Same instance for chained method calls.
     */
    public function setId(idType $quantityUnitId);
    
    
    /**
     * Returns the quantity unit name.
     *
     * @param \LanguageCode $languageCode Language code for language specific name value.
     *
     * @return string Language specific name value.
     */
    public function getName(LanguageCode $languageCode);
    
    
    /**
     * Sets the quantity unit name.
     *
     * @param \StringType   $name         Name value to be set.
     * @param \LanguageCode $languageCode Language code for language specific name value.
     *
     * @return $this|\QuantityUnitInterface Same instance for chained method calls.
     */
    public function setName(StringType $name, LanguageCode $languageCode);
    
    
    /**
     * Returns all language specific names.
     *
     * @return array Language specific names value.
     */
    public function getNames();
    
}