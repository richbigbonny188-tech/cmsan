<?php
/* --------------------------------------------------------------
   ThemeDetailsSerializer.php 2019-01-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\AdminFeed\Services\ShopInformation\Serializer;

use Gambio\AdminFeed\Services\ShopInformation\ValueObjects\ThemeDetails;
use InvalidArgumentException;

/**
 * Class ThemeDetailsSerializer
 *
 * @package Gambio\AdminFeed\Services\ShopInformation\Serializer
 */
class ThemeDetailsSerializer
{
    /**
     * Serializes a given ThemeDetails instance.
     *
     * @param ThemeDetails $themeDetails
     *
     * @return array
     */
    public function serialize(ThemeDetails $themeDetails)
    {
        $json = [
            'available' => $themeDetails->available(),
            'selected'  => $themeDetails->selected(),
            'version'   => $themeDetails->version(),
        ];
        
        return $json;
    }
    
    
    /**
     * Returns a new ThemeDetails instance by using the data of a given array or json strings.
     *
     * @param string|array $json
     *
     * @return ThemeDetails
     */
    public function deserialize($json)
    {
        if (!is_array($json)) {
            $json = json_decode($json, true);
        }
        
        if (!isset($json['available'])
            || !isset($json['selected'])
            || !isset($json['version'])) {
            throw new InvalidArgumentException('Given argument is invalid. Needed property is missing.');
        }
        
        return ThemeDetails::create($json['available'], $json['selected'], $json['version']);
    }
}