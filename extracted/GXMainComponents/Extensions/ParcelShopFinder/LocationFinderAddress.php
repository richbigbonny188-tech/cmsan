<?php
/* --------------------------------------------------------------
   LocationFinderAddress.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

class LocationFinderAddress
{
    /**
     * @var string
     */
    protected $countryCode;
    
    /**
     * @var string
     */
    protected $addressLocality;
    
    /**
     * @var string
     */
    protected $postalCode;
    
    /**
     * @var string
     */
    protected $streetAddress;
    
    
    public function __construct(
        string $countryCode = 'DE',
        string $addressLocality = '',
        string $postalCode = '',
        string $streetAddress = ''
    ) {
        
        $this->countryCode     = $countryCode;
        $this->addressLocality = $addressLocality;
        $this->postalCode      = $postalCode;
        $this->streetAddress   = $streetAddress;
    }
    
    
    public function asArray(): array
    {
        return [
            'streetAddress'   => $this->streetAddress,
            'postalCode'      => $this->postalCode,
            'addressLocality' => $this->addressLocality,
            'countryCode'     => $this->countryCode,
        ];
    }
}
