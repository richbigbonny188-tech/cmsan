<?php
/*--------------------------------------------------------------
   CountryMapper.php 2022-07-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Country\App\Data;

use Gambio\Admin\Modules\Country\Model\Collections\Countries;
use Gambio\Admin\Modules\Country\Model\Collections\Zones;
use Gambio\Admin\Modules\Country\Model\Country;
use Gambio\Admin\Modules\Country\Model\Entities\Zone;
use Gambio\Admin\Modules\Country\Services\CountryFactory;

/**
 * Class CountryMapper
 *
 * @package Gambio\Admin\Modules\Country\App\Data
 */
class CountryMapper extends CountryFactory
{
    /**
     * @param array $data
     *
     * @return Countries
     */
    public function mapCountries(array $data): Countries
    {
        $countries = array_map([$this, 'mapCountry'], $data);
        
        return $this->createCountries(...$countries);
    }
    
    
    /**
     * @param array $data
     *
     * @return Country
     */
    public function mapCountry(array $data): Country
    {
        [
            'countries_id'         => $id,
            'name'                 => $name,
            'address_format'       => $addressFormat,
            'countries_iso_code_2' => $isoCode2,
            'countries_iso_code_3' => $isoCode3,
            'is_active'            => $isActive,
        ] = $data;
    
        $id    = $this->createCountryId((int)$id);
        $zones = $this->mapZones($data['zones']);
    
        return Country::create($id, $name, $zones, $isoCode2, $isoCode3, $addressFormat, $isActive === 'true');
    }
    
    
    /**
     * @param array $zones
     *
     * @return Zones
     */
    private function mapZones(array $zones): Zones
    {
        $zones = array_map([$this, 'mapZone'], $zones);
        
        return $this->createZones(...$zones);
    }
    
    
    /**
     * @param array $zone
     *
     * @return Zone
     */
    private function mapZone(array $zone): Zone
    {
        [
            'zone_id'   => $id,
            'zone_code' => $code,
            'zone_name' => $name,
        ] = $zone;
        
        return $this->createZone($this->createZoneId((int)$id), $code, $name);
    }
}