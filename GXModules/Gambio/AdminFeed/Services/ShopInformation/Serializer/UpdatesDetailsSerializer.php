<?php
/* --------------------------------------------------------------
   UpdatesDetailsSerializer.php 2018-08-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\AdminFeed\Services\ShopInformation\Serializer;

use Gambio\AdminFeed\Services\ShopInformation\Collections\UpdateDetailsCollection;
use Gambio\AdminFeed\Services\ShopInformation\ValueObjects\UpdatesDetails;
use InvalidArgumentException;

/**
 * Class UpdatesDetailsSerializer
 *
 * @package Gambio\AdminFeed\Services\ShopInformation\Serializer
 */
class UpdatesDetailsSerializer
{
    /**
     * @var UpdateDetailsSerializer
     */
    private $updateDetailsSerializer;
    
    
    /**
     * UpdatesDetailsSerializer constructor.
     *
     * @param UpdateDetailsSerializer $updateDetailsSerializer
     */
    public function __construct(UpdateDetailsSerializer $updateDetailsSerializer)
    {
        $this->updateDetailsSerializer = $updateDetailsSerializer;
    }
    
    
    /**
     * Serializes a given UpdatesDetails instance.
     *
     * @param UpdatesDetails $updatesDetails
     *
     * @return array
     */
    public function serialize(UpdatesDetails $updatesDetails)
    {
        $installedData = [];
        foreach ($updatesDetails->installed() as $update) {
            $installedData[] = $this->updateDetailsSerializer->serialize($update);
        }
        
        $downloadedData = [];
        foreach ($updatesDetails->downloaded() as $update) {
            $downloadedData[] = $this->updateDetailsSerializer->serialize($update);
        }
        
        $json = [
            'installed'  => $installedData,
            'downloaded' => $downloadedData,
        ];
        
        return $json;
    }
    
    
    /**
     * Returns a new UpdatesDetails instance by using the data of a given array or json strings.
     *
     * @param string|array $json
     *
     * @return UpdatesDetails
     */
    public function deserialize($json)
    {
        if (!is_array($json)) {
            $json = json_decode($json, true);
        }
        
        if (!isset($json['installed'])
            || !isset($json['downloaded'])) {
            throw new InvalidArgumentException('Given argument is invalid. Needed property is missing.');
        }
        
        $installedUpdates = [];
        foreach ($json['installed'] as $updateData) {
            $installedUpdates[] = $this->updateDetailsSerializer->deserialize($updateData);
        }
        $installed = new UpdateDetailsCollection($installedUpdates);
        
        $downloadedUpdates = [];
        foreach ($json['downloaded'] as $updateData) {
            $downloadedUpdates[] = $this->updateDetailsSerializer->deserialize($updateData);
        }
        $downloaded = new UpdateDetailsCollection($downloadedUpdates);
        
        return UpdatesDetails::create($installed, $downloaded);
    }
}