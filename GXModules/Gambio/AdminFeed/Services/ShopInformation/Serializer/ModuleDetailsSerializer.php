<?php
/* --------------------------------------------------------------
   ModuleDetailsSerializer.php 2018-08-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\AdminFeed\Services\ShopInformation\Serializer;

use Gambio\AdminFeed\Services\ShopInformation\ValueObjects\ModuleDetails;
use InvalidArgumentException;

/**
 * Class ModuleDetailsSerializer
 *
 * @package Gambio\AdminFeed\Services\ShopInformation\Serializer
 */
class ModuleDetailsSerializer
{
    /**
     * Serializes a given ModuleDetails instance.
     *
     * @param ModuleDetails $moduleDetails
     *
     * @return array
     */
    public function serialize(ModuleDetails $moduleDetails)
    {
        $json = [
            'name'      => $moduleDetails->name(),
            'installed' => $moduleDetails->installed(),
            'enabled'   => $moduleDetails->enabled(),
        ];
        
        return $json;
    }
    
    
    /**
     * Returns a new ModuleDetails instance by using the data of a given array or json strings.
     *
     * @param string|array $json
     *
     * @return ModuleDetails
     */
    public function deserialize($json)
    {
        if (!is_array($json)) {
            $json = json_decode($json, true);
        }
        
        if (!isset($json['name'])
            || !isset($json['installed'])
            || !isset($json['enabled'])) {
            throw new InvalidArgumentException('Given argument is invalid. Needed property is missing.');
        }
        
        return ModuleDetails::create($json['name'], $json['installed'], $json['enabled']);
    }
}