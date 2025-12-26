<?php
/* --------------------------------------------------------------
   PhpServerDetailsSerializer.php 2018-08-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\AdminFeed\Services\ShopInformation\Serializer;

use Gambio\AdminFeed\Services\ShopInformation\ValueObjects\PhpServerDetails;
use InvalidArgumentException;

/**
 * Class PhpServerDetailsSerializer
 *
 * @package Gambio\AdminFeed\Services\ShopInformation\Serializer
 */
class PhpServerDetailsSerializer
{
    /**
     * Serializes a given PhpServerDetails instance.
     *
     * @param PhpServerDetails $phpServerDetails
     *
     * @return array
     */
    public function serialize(PhpServerDetails $phpServerDetails)
    {
        $json = [
            'version'       => $phpServerDetails->version(),
            'extensions'    => $phpServerDetails->extensions(),
            'configuration' => $phpServerDetails->configuration(),
        ];
        
        return $json;
    }
    
    
    /**
     * Returns a new PhpServerDetails instance by using the data of a given array or json strings.
     *
     * @param string|array $json
     *
     * @return PhpServerDetails
     */
    public function deserialize($json)
    {
        if (!is_array($json)) {
            $json = json_decode($json, true);
        }
        
        if (!isset($json['version'])
            || !isset($json['extensions'])
            || !isset($json['configuration'])) {
            throw new InvalidArgumentException('Given argument is invalid. Needed property is missing.');
        }
        
        return PhpServerDetails::create($json['version'], $json['extensions'], $json['configuration']);
    }
}