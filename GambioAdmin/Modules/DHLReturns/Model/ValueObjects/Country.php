<?php
/* --------------------------------------------------------------
   Country.php 2021-04-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\DHLReturns\Model\ValueObjects;

class Country
{
    /**
     * @var string
     */
    private $countryISOCode;
    /**
     * @var string
     */
    private $country;
    /**
     * @var string
     */
    private $state;
    
    
    /**
     * Country constructor.
     */
    public function __construct(string $countryISOCode)
    {
        $this->countryISOCode = $countryISOCode;
        $this->country        = '';
        $this->state          = '';
    }
    
    
    /**
     * @return string
     */
    public function getCountryISOCode(): string
    {
        return $this->countryISOCode;
    }
    
    
    /**
     * @param string $countryISOCode
     */
    public function setCountryISOCode(string $countryISOCode): void
    {
        $this->countryISOCode = $countryISOCode;
    }
    
    
    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }
    
    
    /**
     * @param string $country
     */
    public function setCountry(string $country): void
    {
        $this->country = $country;
    }
    
    
    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }
    
    
    /**
     * @param string $state
     */
    public function setState(string $state): void
    {
        $this->state = $state;
    }
}
