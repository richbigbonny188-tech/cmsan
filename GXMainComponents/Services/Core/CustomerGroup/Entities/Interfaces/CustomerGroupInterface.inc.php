<?php
/* --------------------------------------------------------------
   CustomerGroupInterface.inc.php 2018-02-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerGroupInterface
 *
 * @category   System
 * @package    CustomerGroup
 * @subpackage Entities
 */
interface CustomerGroupInterface
{
    /**
     * Saves customer group in database.
     *
     * @return $this|\CustomerGroupInterface Same instance for chained method calls.
     */
    public function store();
    
    
    /**
     * Create base data from chosen personal offers table.
     *
     * @param \IntType $baseId
     *
     * @return \CustomerGroup
     */
    public function createBase(IntType $baseId);
    
    
    /**
     * Updates customer group in database.
     *
     * @return $this|\CustomerGroupInterface Same instance for chained method calls.
     */
    public function update();
    
    
    /**
     * Deletes customer group from database.
     *
     * @return $this|\CustomerGroupInterface Same instance for chained method calls.
     */
    public function delete();
    
    
    /**
     * Returns the customer group id.
     *
     * @return int Customer group id.
     */
    public function getId();
    
    
    /**
     * Sets the customer group id.
     *
     * @param \IntType $id The customer group id to be set.
     *
     * @return $this|\CustomerGroupInterface Same instance for chained method calls.
     */
    public function setId(IntType $id);
    
    
    /**
     * Returns the customer group default status.
     *
     * @return bool Customer group default status.
     */
    public function isDefault();
    
    
    /**
     * Sets the customer group id.
     *
     * @param \BoolType $default The customer group default group to set.
     *
     * @return $this|\CustomerGroupInterface Same instance for chained method calls.
     */
    public function setDefault(BoolType $default);
    
    
    /**
     * Returns the name.
     *
     * @param \LanguageCode $languageCode Language code for language specific name value.
     *
     * @return string Language specific name value.
     */
    public function getName(LanguageCode $languageCode);
    
    
    /**
     * Sets the name.
     *
     * @param \StringType   $name         Name.
     * @param \LanguageCode $languageCode Language code for language specific name value.
     *
     * @return $this|\CustomerGroupInterface Same instance for chained method calls.
     */
    public function setName(StringType $name, LanguageCode $languageCode);
    
    
    /**
     * Returns all language specific name.
     *
     * @return array Language specific name value.
     */
    public function getNames();
    
    
    /**
     * Returns the customer group settings.
     *
     * @return \CustomerGroupSettingsInterface
     */
    public function getSettings();
    
    
    /**
     * Sets the customer group settings.
     *
     * @param \CustomerGroupSettingsInterface $settings Customer group settings.
     *
     * @return $this|\CustomerGroupInterface Same instance for chained method calls.
     */
    public function setSettings(CustomerGroupSettingsInterface $settings);
    
    
    /**
     * Returns the customer group configurations.
     *
     * @return \CustomerGroupConfigurationsInterface
     */
    public function getConfigurations();
    
    
    /**
     * Sets the customer group configurations.
     *
     * @param \CustomerGroupConfigurationsInterface $configurations Customer group configurations.
     *
     * @return $this|\CustomerGroupInterface Same instance for chained method calls.
     */
    public function setConfigurations(CustomerGroupConfigurationsInterface $configurations);
    
    
    /**
     * Sets the number of members to the customer group.
     *
     * @param \IntType $number
     *
     * @return \CustomerGroup
     */
    public function setMembers(IntType $number);
    
    
    /**
     * Returns the customer group members number.
     *
     * @return int
     */
    public function getMembers();
}