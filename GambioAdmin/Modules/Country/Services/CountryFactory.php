<?php
/*--------------------------------------------------------------
   CountryFactory.php 2022-07-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Country\Services;

use Gambio\Admin\Modules\Country\Model\Collections\Countries;
use Gambio\Admin\Modules\Country\Model\Collections\Zones;
use Gambio\Admin\Modules\Country\Model\Country;
use Gambio\Admin\Modules\Country\Model\Entities\Zone;
use Gambio\Admin\Modules\Country\Model\ValueObjects\CountryId;
use Gambio\Admin\Modules\Country\Model\ValueObjects\ZoneId;

/**
 * Class CountryFactory
 *
 * @package Gambio\Admin\Modules\Country\Services
 */
class CountryFactory
{
    /**
     * @param int $countryId
     *
     * @return CountryId
     */
    public function createCountryId(int $countryId): CountryId
    {
        return CountryId::create($countryId);
    }
    
    
    /**
     * @param Country ...$countries
     *
     * @return Countries
     */
    public function createCountries(Country ...$countries): Countries
    {
        return Countries::create(...$countries);
    }
    
    
    /**
     * @param int $zoneId
     *
     * @return ZoneId
     */
    public function createZoneId(int $zoneId): ZoneId
    {
        return ZoneId::create($zoneId);
    }
    
    
    /**
     * @param ZoneId $id
     * @param string $code
     * @param string $name
     *
     * @return Zone
     */
    public function createZone(
        ZoneId $id,
        string $code,
        string $name
    ): Zone {
        
        return Zone::create($id, $code, $name);
    }
    
    
    /**
     * @param Zone ...$zones
     *
     * @return Zones
     */
    public function createZones(Zone ...$zones): Zones
    {
        return Zones::create(...$zones);
    }
}