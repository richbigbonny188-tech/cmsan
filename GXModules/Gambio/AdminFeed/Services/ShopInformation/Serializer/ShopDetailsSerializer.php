<?php
/* --------------------------------------------------------------
   ShopDetailsSerializer.php 2018-08-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\AdminFeed\Services\ShopInformation\Serializer;

use Gambio\AdminFeed\Services\ShopInformation\ValueObjects\ShopDetails;
use InvalidArgumentException;

/**
 * Class ShopDetailsSerializer
 *
 * @package Gambio\AdminFeed\Services\ShopInformation\Serializer
 */
class ShopDetailsSerializer
{
    /**
     * Serializes a given ShopDetails instance.
     *
     * @param ShopDetails $shopDetails
     *
     * @return array
     */
    public function serialize(ShopDetails $shopDetails)
    {
        $json = [
            'version'         => $shopDetails->version(),
            'url'             => $shopDetails->url(),
            'key'             => $shopDetails->key(),
            'languages'       => $shopDetails->languages(),
            'defaultLanguage' => $shopDetails->defaultLanguage(),
            'countries'       => $shopDetails->countries(),
        ];
        
        return $json;
    }
    
    
    /**
     * Returns a new ShopDetails instance by using the data of a given array or json strings.
     *
     * @param string|array $json
     *
     * @return ShopDetails
     */
    public function deserialize($json)
    {
        if (!is_array($json)) {
            $json = json_decode($json, true);
        }
        
        if (!isset($json['version'])
            || !isset($json['url'])
            || !isset($json['key'])
            || !isset($json['languages'])
            || !isset($json['defaultLanguage'])
            || !isset($json['countries'])) {
            throw new InvalidArgumentException('Given argument is invalid. Needed property is missing.');
        }
        
        return ShopDetails::create($json['version'],
                                   $json['url'],
                                   $json['key'],
                                   $json['languages'],
                                   $json['defaultLanguage'],
                                   $json['countries']);
    }
}