<?php

/* --------------------------------------------------------------
   VPEInterface.inc.php 2017-07-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface VPEInterface
 *
 * @category   System
 * @package    VPE
 * @subpackage Entities
 */
interface VPEInterface
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
     * @param \IdType $vpeId Id to be set.
     *
     * @return $this|\VPEInterface Same instance for chained method calls.
     */
    public function setId(IdType $vpeId);
    
    
    /**
     * Returns the language specific name.
     *
     * @param \LanguageCode $languageCode Language code for language specific name value.
     *
     * @return string Language specific name value.
     */
    public function getName(LanguageCode $languageCode);
    
    
    /**
     * Returns all language specific names.
     *
     * @return array Language specific names value.
     */
    public function getNames();
    
    
    /**
     * Sets the language specific name.
     *
     * @param \StringType   $name         Name value to be set.
     * @param \LanguageCode $languageCode Language code for language specific name value.
     *
     * @return $this|\VPEInterface Same instance for chained method calls.
     */
    public function setName(StringType $name, LanguageCode $languageCode);
    
    
    /**
     * Returns true if the vpe entity instance is the shops default.
     *
     * @return bool True if VPE value is shops default.
     */
    public function isDefault();
    
    
    /**
     * Sets vpe entity to shops default, if bool type is true.
     *
     * @param \BoolType $default Is current vpe entity shops default?
     *
     * @return $this|\VPEInterface Same instance for chained method calls.
     */
    public function setDefault(BoolType $default);
}
