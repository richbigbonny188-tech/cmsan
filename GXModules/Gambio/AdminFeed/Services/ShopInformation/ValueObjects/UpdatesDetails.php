<?php
/* --------------------------------------------------------------
   UpdatesDetails.php 2018-08-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\AdminFeed\Services\ShopInformation\ValueObjects;

use Gambio\AdminFeed\Services\ShopInformation\Collections\UpdateDetailsCollection;

/**
 * Class UpdatesDetails
 *
 * @package Gambio\AdminFeed\Services\ShopInformation\ValueObjects
 */
class UpdatesDetails
{
    /**
     * @var UpdateDetailsCollection
     */
    private $installed;
    
    /**
     * @var UpdateDetailsCollection
     */
    private $downloaded;
    
    
    /**
     * UpdatesDetails constructor.
     *
     * @param UpdateDetailsCollection $installed
     * @param UpdateDetailsCollection $downloaded
     */
    public function __construct(UpdateDetailsCollection $installed, UpdateDetailsCollection $downloaded)
    {
        $this->installed  = $installed;
        $this->downloaded = $downloaded;
    }
    
    
    /**
     * Creates and returns a new UpdatesDetails instance.
     *
     * @param UpdateDetailsCollection $installed
     * @param UpdateDetailsCollection $downloaded
     *
     * @return UpdatesDetails
     */
    static function create(UpdateDetailsCollection $installed, UpdateDetailsCollection $downloaded)
    {
        return new self($installed, $downloaded);
    }
    
    
    /**
     * Returns a collection of installed updates.
     *
     * @return UpdateDetailsCollection
     */
    public function installed()
    {
        return $this->installed;
    }
    
    
    /**
     * Returns a collection of updates, that had been downloaded with the AutoUpdater but not yet installed.
     *
     * @return UpdateDetailsCollection
     */
    public function downloaded()
    {
        return $this->downloaded;
    }
}