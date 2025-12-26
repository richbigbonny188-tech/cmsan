<?php

/* --------------------------------------------------------------
  CustomerGroup.inc.php 2018-02-01
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2017 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Class QuantityUnitCollection
 *
 * @category   System
 * @package    CustomerGroup
 * @subpackage Entities
 */
class CustomerGroup implements CustomerGroupInterface
{
    /**
     * Customer group id.
     *
     * @var int
     */
    protected $id = 0;
    
    /**
     * Customer group member number.
     *
     * @var int
     */
    protected $members = 0;
    
    /**
     * Customer Group default value.
     *
     * @var bool
     */
    protected $default = false;
    
    /**
     * @var \EditableKeyValueCollection
     */
    protected $names;
    
    /**
     * @var CustomerGroupSettingsInterface
     */
    protected $settings;
    
    /**
     * @var CustomerGroupConfigurationsInterface
     */
    protected $configurations;
    
    /**
     * @var CustomerGroupRepositoryInterface
     */
    protected $repository;
    
    
    /**
     * CustomerGroup constructor.
     *
     * @param \CustomerGroupRepositoryInterface     $repository
     * @param \EditableKeyValueCollection           $names
     * @param \CustomerGroupConfigurationsInterface $configurations
     * @param \CustomerGroupSettingsInterface       $settings
     */
    public function __construct(
        CustomerGroupRepositoryInterface $repository,
        EditableKeyValueCollection $names,
        CustomerGroupConfigurationsInterface $configurations,
        CustomerGroupSettingsInterface $settings
    ) {
        $this->repository     = $repository;
        $this->names          = $names;
        $this->configurations = $configurations;
        $this->settings       = $settings;
    }
    
    
    /**
     * Saves customer group in database.
     *
     * @return $this|\CustomerGroupInterface Same instance for chained method calls.
     */
    public function store()
    {
        $this->repository->store($this);
        
        return $this;
    }
    
    
    /**
     * Create base data from chosen personal offers table.
     *
     * @param \IntType $baseId
     *
     * @return \CustomerGroup
     */
    public function createBase(IntType $baseId)
    {
        $this->repository->createBase(new IntType($this->getId()), $baseId);
        
        return $this;
    }
    
    
    /**
     * Updates customer group in database.
     *
     * @return $this|\CustomerGroupInterface Same instance for chained method calls.
     */
    public function update()
    {
        $this->repository->update($this);
        
        return $this;
    }
    
    
    /**
     * Deletes customer group entity data in database with personal offer table.
     *
     * @return $this|\CustomerGroupInterface Same instance for chained method calls.
     */
    public function delete()
    {
        $this->repository->delete($this);
        
        return $this;
    }
    
    
    /**
     * Returns the customer group id.
     *
     * @return int Customer group id.
     */
    public function getId()
    {
        return $this->id;
    }
    
    
    /**
     * Sets the customer group id.
     *
     * @param \IntType $id The customer group id to be set.
     *
     * @return $this|\CustomerGroupInterface Same instance for chained method calls.
     */
    public function setId(IntType $id)
    {
        $this->id = $id->asInt();
        
        return $this;
    }
    
    
    /**
     * Returns the name.
     *
     * @param \LanguageCode $languageCode Language code for language specific name value.
     *
     * @return string Language specific name value.
     */
    public function getName(LanguageCode $languageCode)
    {
        return $this->names->getValue($languageCode->asString());
    }
    
    
    /**
     * Sets the name.
     *
     * @param \StringType   $name         Name.
     * @param \LanguageCode $languageCode Language code for language specific name value.
     *
     * @return $this|\CustomerGroupInterface Same instance for chained method calls.
     */
    public function setName(StringType $name, LanguageCode $languageCode)
    {
        $this->names->setValue($languageCode->asString(), $name->asString());
        
        return $this;
    }
    
    
    /**
     * Returns all language specific name.
     *
     * @return array Language specific name value.
     */
    public function getNames()
    {
        return $this->names->getArray();
    }
    
    
    /**
     * Returns the customer group settings.
     *
     * @return \CustomerGroupSettingsInterface
     */
    public function getSettings()
    {
        return $this->settings;
    }
    
    
    /**
     * Sets the customer group settings.
     *
     * @param \CustomerGroupSettingsInterface $settings Customer group settings.
     *
     * @return $this|\CustomerGroupInterface Same instance for chained method calls.
     */
    public function setSettings(CustomerGroupSettingsInterface $settings)
    {
        $this->settings = $settings;
        
        return $this;
    }
    
    
    /**
     * Returns the customer group configurations.
     *
     * @return \CustomerGroupConfigurationsInterface
     */
    public function getConfigurations()
    {
        return $this->configurations;
    }
    
    
    /**
     * Sets the customer group configurations.
     *
     * @param \CustomerGroupConfigurationsInterface $configurations Customer group configurations.
     *
     * @return $this|\CustomerGroupInterface Same instance for chained method calls.
     */
    public function setConfigurations(CustomerGroupConfigurationsInterface $configurations)
    {
        $this->configurations = $configurations;
        
        return $this;
    }
    
    
    /**
     * Returns the customer group default status.
     *
     * @return bool Customer group default status.
     */
    public function isDefault()
    {
        return $this->default;
    }
    
    
    /**
     * Sets the customer group id.
     *
     * @param \BoolType $default The customer group default group to set.
     *
     * @return $this|\CustomerGroupInterface Same instance for chained method calls.
     */
    public function setDefault(BoolType $default)
    {
        $this->default = $default->asBool();
        
        return $this;
    }
    
    
    /**
     * Returns the customer group members number.
     *
     * @return int
     */
    public function getMembers()
    {
        return $this->members;
    }
    
    
    /**
     * Sets the number of members to the customer group.
     *
     * @param \IntType $number
     *
     * @return \CustomerGroup
     */
    public function setMembers(IntType $number)
    {
        $this->members = $number->asInt();
        
        return $this;
    }
}