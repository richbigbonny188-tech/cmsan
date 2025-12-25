<?php
/* --------------------------------------------------------------
   UpdatesDetailsMapper.php 2018-08-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\AdminFeed\Services\ShopInformation\Mapper;

use Gambio\AdminFeed\Services\ShopInformation\Collections\UpdateDetailsCollection;
use Gambio\AdminFeed\Services\ShopInformation\Reader\UpdatesDetailsReader;
use Gambio\AdminFeed\Services\ShopInformation\ValueObjects\UpdateDetails;
use Gambio\AdminFeed\Services\ShopInformation\ValueObjects\UpdatesDetails;

/**
 * Class UpdatesDetailsMapper
 *
 * @package Gambio\AdminFeed\Services\ShopInformation\Mapper
 */
class UpdatesDetailsMapper
{
    /**
     * @var UpdatesDetailsReader
     */
    private $reader;
    
    
    /**
     * UpdatesDetailsMapper constructor.
     *
     * @param UpdatesDetailsReader $reader
     */
    public function __construct(UpdatesDetailsReader $reader)
    {
        $this->reader = $reader;
    }
    
    
    /**
     * Returns the updates details.
     *
     * @return UpdatesDetails
     */
    public function getUpdatesDetails()
    {
        $installedUpdates = $this->collectInstalledUpdates();
        
        $downloadedUpdates = $this->collectDownloadedUpdates();
        
        return new UpdatesDetails($installedUpdates, $downloadedUpdates);
    }
    
    
    /**
     * @return UpdateDetailsCollection
     */
    private function collectInstalledUpdates()
    {
        $installedUpdates = [];
        foreach ($this->reader->getInstalledUpdatesData() as $updateData) {
            $installedUpdates[] = new UpdateDetails($updateData['name'],
                                                    $updateData['version'],
                                                    $updateData['installation_date']);
        }
        
        return new UpdateDetailsCollection($installedUpdates);
    }
    
    
    /**
     * @return UpdateDetailsCollection
     */
    private function collectDownloadedUpdates()
    {
        $downloadedUpdates = [];
        foreach ($this->reader->getDownloadedUpdatesData() as $updateData) {
            if (!isset($updateData['name']) || !isset($updateData['version']) || !isset($updateData['date'])) {
                continue;
            }
            
            $downloadedUpdates[] = new UpdateDetails($updateData['name'], $updateData['version'], $updateData['date']);
        }
        
        return new UpdateDetailsCollection($downloadedUpdates);
    }
}