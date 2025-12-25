<?php

/* --------------------------------------------------------------
   VersionInfoService.inc.php 2022-03-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class VersionInfoService
 *
 * This class provides methods for retrieving data of a particular category and a collection of specific categories.
 *
 * @category   Core
 * @package    VersionInfo
 */
class VersionInfoService
{
    /**
     * @var VersionInfoRepository
     */
    protected $repository;
    
    
    /**
     * VersionInfoService constructor.
     *
     * @param VersionInfoRepository $repository
     */
    public function __construct(VersionInfoRepository $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * Returns an array of VersionInformationItemInterface objects.
     *
     * @return VersionInfoItemInterface[]
     */
    public function getAllVersionInfoItems()
    {
        return $this->repository->getAllVersionInfoItems();
    }
    
    
    /**
     * Returns the last installed version
     *
     * @return VersionInfoItemInterface|null
     */
    public function getLastInstalledVersion()
    {
        return $this->repository->getLastInstalledVersion();
    }
}
