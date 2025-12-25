<?php
/* --------------------------------------------------------------
   CustomerDisplaySettings.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CustomerDisplaySettings
 */
class CustomerDisplaySettings
{
    /**
     * @var bool
     */
    protected $gender;
    
    /**
     * @var bool
     */
    protected $dateOfBirth;
    
    /**
     * @var bool
     */
    protected $company;
    
    /**
     * @var bool
     */
    protected $countryZone;
    
    /**
     * @var bool
     */
    protected $telephone;
    
    /**
     * @var bool
     */
    protected $fax;
    
    /**
     * @var bool
     */
    protected $suburb;
    
    
    /**
     * CustomerDisplaySettings constructor.
     *
     * @param \BoolType $gender      Should the customers gender be displayed?
     * @param \BoolType $dateOfBirth Should the customers date of birth be displayed?
     * @param \BoolType $company     Should the customers company be displayed?
     * @param \BoolType $countryZone Should the customers country zone be displayed?
     * @param \BoolType $telephone   Should the customers telephone be displayed?
     * @param \BoolType $fax         Should the customers fax be displayed?
     * @param \BoolType $suburb      Should the customers suburb be displayed?
     */
    public function __construct(
        \BoolType $gender,
        \BoolType $dateOfBirth,
        \BoolType $company,
        \BoolType $countryZone,
        \BoolType $telephone,
        \BoolType $fax,
        \BoolType $suburb
    ) {
        $this->gender      = $gender->asBool();
        $this->dateOfBirth = $dateOfBirth->asBool();
        $this->company     = $company->asBool();
        $this->countryZone = $countryZone->asBool();
        $this->telephone   = $telephone->asBool();
        $this->fax         = $fax->asBool();
        $this->suburb      = $suburb->asBool();
    }
    
    
    /**
     * Should the customers gender be displayed?
     *
     * @return bool
     */
    public function gender()
    {
        return $this->gender;
    }
    
    
    /**
     * Should the customers date of birth be displayed?
     *
     * @return bool
     */
    public function dateOfBirth()
    {
        return $this->dateOfBirth;
    }
    
    
    /**
     * Should the customers company be displayed?
     *
     * @return bool
     */
    public function company()
    {
        return $this->company;
    }
    
    
    /**
     * Should the customers country zone be displayed?
     *
     * @return bool
     */
    public function countryZone()
    {
        return $this->countryZone;
    }
    
    
    /**
     * Should the customers telephone be displayed?
     *
     * @return bool
     */
    public function telephone()
    {
        return $this->telephone;
    }
    
    
    /**
     * Should the customers fax be displayed?
     *
     * @return bool
     */
    public function fax()
    {
        return $this->fax;
    }
    
    
    /**
     * Should the customers suburb be displayed?
     *
     * @return bool
     */
    public function suburb()
    {
        return $this->suburb;
    }
}