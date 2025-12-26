<?php
/* --------------------------------------------------------------
   CustomerAddress.php 2020-08-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Withdrawal\Model\ValueObjects;

/**
 * Class CustomerAddress
 *
 * @package Gambio\Admin\Modules\Withdrawal\Model\ValueObjects
 */
class CustomerAddress
{
    /**
     * @var string
     */
    private $street;
    
    /**
     * @var string
     */
    private $postcode;
    
    /**
     * @var string
     */
    private $city;
    
    /**
     * @var string
     */
    private $country;
    
    
    /**
     * CustomerAddress constructor.
     *
     * @param string $street
     * @param string $postcode
     * @param string $city
     * @param string $country
     */
    private function __construct(string $street, string $postcode, string $city, string $country)
    {
        $this->street   = $street;
        $this->postcode = $postcode;
        $this->city     = $city;
        $this->country  = $country;
    }
    
    
    /**
     * @param string $street
     * @param string $postcode
     * @param string $city
     * @param string $country
     *
     * @return CustomerAddress
     */
    public static function create(string $street, string $postcode, string $city, string $country): CustomerAddress
    {
        return new CustomerAddress($street, $postcode, $city, $country);
    }
    
    
    /**
     * @return string
     */
    public function street(): string
    {
        return $this->street;
    }
    
    
    /**
     * @return string
     */
    public function postcode(): string
    {
        return $this->postcode;
    }
    
    
    /**
     * @return string
     */
    public function city(): string
    {
        return $this->city;
    }
    
    
    /**
     * @return string
     */
    public function country(): string
    {
        return $this->country;
    }
    
    
    /**
     * Array serialization.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'street'   => $this->street(),
            'postcode' => $this->postcode(),
            'city'     => $this->city(),
            'country'  => $this->country(),
        ];
    }
}