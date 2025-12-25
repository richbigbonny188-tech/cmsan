<?php
/* --------------------------------------------------------------
   MysqlServerDetailsSerializer.php 2018-08-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\AdminFeed\Services\ShopInformation\Serializer;

use Gambio\AdminFeed\Services\ShopInformation\ValueObjects\MysqlServerDetails;
use InvalidArgumentException;

/**
 * Class MysqlServerDetailsSerializer
 *
 * @package Gambio\AdminFeed\Services\ShopInformation\Serializer
 */
class MysqlServerDetailsSerializer
{
    /**
     * Serializes a given MysqlServerDetails instance.
     *
     * @param MysqlServerDetails $mysqlServerDetails
     *
     * @return array
     */
    public function serialize(MysqlServerDetails $mysqlServerDetails)
    {
        $json = [
            'version'       => $mysqlServerDetails->version(),
            'engines'       => $mysqlServerDetails->engines(),
            'defaultEngine' => $mysqlServerDetails->defaultEngine(),
        ];
        
        return $json;
    }
    
    
    /**
     * Returns a new MysqlServerDetails instance by using the data of a given array or json strings.
     *
     * @param string|array $json
     *
     * @return MysqlServerDetails
     */
    public function deserialize($json)
    {
        if (!is_array($json)) {
            $json = json_decode($json, true);
        }
        
        if (!isset($json['version'])
            || !isset($json['engines'])
            || !isset($json['defaultEngine'])) {
            throw new InvalidArgumentException('Given argument is invalid. Needed property is missing.');
        }
        
        return MysqlServerDetails::create($json['version'], $json['engines'], $json['defaultEngine']);
    }
}