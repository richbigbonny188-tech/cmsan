<?php
/*--------------------------------------------------------------
   Country.php 2022-07-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Country\Model;

use Gambio\Admin\Modules\Country\Model\Collections\Zones;
use Gambio\Admin\Modules\Country\Model\ValueObjects\CountryId;
use Webmozart\Assert\Assert;

/**
 * Class Country
 *
 * @package Gambio\Admin\Modules\Country\Model
 */
class Country
{
    private CountryId $id;
    private string    $name;
    private Zones     $zones;
    private string    $isoCode2;
    private string    $isoCode3;
    private string    $addressFormat;
    private bool      $isActive;
    
    
    /**
     * @param CountryId $id
     * @param string    $name
     * @param Zones     $zones
     * @param string    $isoCode2
     * @param string    $isoCode3
     * @param string    $addressFormat
     * @param bool      $isActive
     */
    private function __construct(
        CountryId $id,
        string    $name,
        Zones     $zones,
        string    $isoCode2,
        string    $isoCode3,
        string    $addressFormat,
        bool      $isActive
    ) {
        $this->id            = $id;
        $this->name          = $name;
        $this->zones         = $zones;
        $this->isoCode2      = $isoCode2;
        $this->isoCode3      = $isoCode3;
        $this->addressFormat = $addressFormat;
        $this->isActive      = $isActive;
    }
    
    
    /**
     * @param CountryId $id
     * @param string    $name
     * @param Zones     $zones
     * @param string    $isoCode2
     * @param string    $isoCode3
     * @param string    $addressFormat
     * @param bool      $isActive
     *
     * @return Country
     */
    public static function create(
        CountryId $id,
        string    $name,
        Zones     $zones,
        string    $isoCode2,
        string    $isoCode3,
        string    $addressFormat,
        bool      $isActive
    ): Country {
        
        Assert::length($isoCode2, 2, 'Expected iso code 2 to contain %2$s characters. Got: %s');
        Assert::length($isoCode3, 3, 'Expected iso code 3 to contain %2$s characters. Got: %s');
        
        return new self($id, $name, $zones, $isoCode2, $isoCode3, $addressFormat, $isActive);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id'            => $this->id(),
            'name'          => $this->name(),
            'zones'         => $this->zones()->toArray(),
            'isoCode2'      => $this->isoCode2(),
            'isoCode3'      => $this->isoCode3(),
            'addressFormat' => $this->addressFormat(),
            'isActive'      => $this->isActive(),
        ];
    }
    
    
    /**
     * @return int
     */
    public function id(): int
    {
        return $this->id->value();
    }
    
    
    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }
    
    
    /**
     * @return Zones
     */
    public function zones(): Zones
    {
        return $this->zones;
    }
    
    
    /**
     * @return string
     */
    public function isoCode2(): string
    {
        return $this->isoCode2;
    }
    
    
    /**
     * @return string
     */
    public function isoCode3(): string
    {
        return $this->isoCode3;
    }
    
    
    /**
     * @return string
     */
    public function addressFormat(): string
    {
        return $this->addressFormat;
    }
    
    
    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }
}