<?php
/* --------------------------------------------------------------
   CustomerConfigurationSettings.inc.php 2018-06-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CustomerConfigurationSettings
 */
class CustomerConfigurationSettings
{
    /**
     * @var bool
     */
    protected $optionalNames;
    
    /**
     * @var bool
     */
    protected $acceptPrivacy;
    
    /**
     * @var bool
     */
    protected $splitStreetInformation;
    
    /**
     * @var bool
     */
    protected $genderMandatory;
    
    
    /**
     * CustomerConfigurationSettings constructor.
     *
     * @param \BoolType $optionalNames          Are the customer names optional?
     * @param \BoolType $acceptPrivacy          Must the privacy be accepted.
     * @param \BoolType $splitStreetInformation Should the street information be split?
     * @param \BoolType $genderMandatory        Is the gender field mandatory?
     */
    public function __construct(
        \BoolType $optionalNames,
        \BoolType $acceptPrivacy,
        \BoolType $splitStreetInformation,
        \BoolType $genderMandatory
    ) {
        $this->optionalNames          = $optionalNames->asBool();
        $this->acceptPrivacy          = $acceptPrivacy->asBool();
        $this->splitStreetInformation = $splitStreetInformation->asBool();
        $this->genderMandatory        = $genderMandatory->asBool();
    }
    
    
    /**
     * Are the customer names optional?
     *
     * @return bool
     */
    public function optionalNames()
    {
        return $this->optionalNames;
    }
    
    
    /**
     * Must the privacy be accepted.
     *
     * @return bool
     */
    public function acceptPrivacy()
    {
        return $this->acceptPrivacy;
    }
    
    
    /**
     * Should the street information be split?
     *
     * @return bool
     */
    public function splitStreetInformation()
    {
        return $this->splitStreetInformation;
    }
    
    
    /**
     * Is gender field mandatory?
     *
     * @return bool
     */
    public function genderMandatory()
    {
        return $this->genderMandatory;
    }
}