<?php

/* --------------------------------------------------------------
   VersionInfoRepository.inc.php 2022-03-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class VersionInfoRepository
 *
 * This class provides methods for retrieving data of a particular category and a collection of specific categories.
 *
 * @category   Core
 * @package    VersionInfo
 */
class VersionInfoRepository
{
    /**
     * @var VersionInfoFileReader
     */
    protected $fileReader;
    
    /**
     * @var VersionInfoDatabaseReader
     */
    protected $databaseReader;
    
    
    /**
     * VersionInfoRepository constructor.
     *
     * @param VersionInfoDatabaseReader $databaseReader
     *
     * @param VersionInfoFileReader     $fileReader
     */
    public function __construct(VersionInfoDatabaseReader $databaseReader, VersionInfoFileReader $fileReader)
    {
        $this->fileReader     = $fileReader;
        $this->databaseReader = $databaseReader;
    }
    
    
    /**
     * Returns an array of VersionInfoItemInterface objects.
     *
     * @return VersionInfoItemInterface[]
     */
    public function getAllVersionInfoItems()
    {
        $versionInfoDatabaseItems = $this->databaseReader->getAllVersionInfoItems();
        $versionInfoFileItems     = $this->fileReader->getAllVersionInfoItems();
        
        $this->_markOldVersionsCaseAsTargetVersions($versionInfoFileItems);
        $this->_markAllVersionFileItemsWithFailed($versionInfoFileItems);
        $this->_markInstalledTargetVersions($versionInfoFileItems, $versionInfoDatabaseItems);
        
        $filteredVersionInfoFileItems = array_filter($versionInfoFileItems,
            function ($versionInfoFileItem) use (
                $versionInfoDatabaseItems
            ) {
                $versionFound = false;
                
                /** @var VersionInfoItemInterface $versionInfoFileItem */
                foreach ($versionInfoDatabaseItems as $versionInfoDatabaseItem) {
                    if ($versionInfoDatabaseItem->getInstalledStatus() === false) {
                        $versionInfoDatabaseItem->setForcedInstallationVersion(true);
                    }
                    if ($versionInfoFileItem->getVersion() === $versionInfoDatabaseItem->getVersion()) {
                        $versionFound = true;
                        break;
                    }
                }
                
                return !$versionFound;
            });
        
        $versionInfoItems = array_merge($versionInfoDatabaseItems, $filteredVersionInfoFileItems);
        
        $originInstallationVersionTimestamp = $this->_getTheOriginInstallationVersionTimestamp($versionInfoItems);
        $this->_markTheOldestTargetVersion($versionInfoItems, $originInstallationVersionTimestamp);
        
        $currentInstallationVersionTimestamp = $this->_getTheCurrentInstallationVersionTimestamp($versionInfoItems);
        $this->_markTheNewestTargetVersion($versionInfoItems, $currentInstallationVersionTimestamp);
        
        $this->_marksSpecialVersionNamesAsTargetVersions($versionInfoItems);
        
        @usort($versionInfoItems, [$this, '_sortVersionInfoItemByTime']);
        
        return $versionInfoItems;
    }
    
    
    /**
     * Returns the last installed version or "null" if no results are found.
     *
     * @return VersionInfoDatabaseItem|null
     */
    public function getLastInstalledVersion()
    {
        return $this->databaseReader->getLastInstalledVersion();
    }
    
    
    /**
     * Sorted all Version Info Items by time, newest first
     *
     * @param VersionInfoItemInterface $a
     * @param VersionInfoItemInterface $b
     *
     * @return int
     */
    protected function _sortVersionInfoItemByTime(VersionInfoItemInterface $a, VersionInfoItemInterface $b)
    {
        if ($a->getInstallationDate()->getTimestamp() < $b->getInstallationDate()->getTimestamp()) {
            return 1;
        } elseif ($a->getInstallationDate()->getTimestamp() > $b->getInstallationDate()->getTimestamp()) {
            return -1;
        } elseif ($a->getInstallationDate()->getTimestamp() == $b->getInstallationDate()->getTimestamp()) {
            return version_compare($a->getVersion(), $b->getVersion()) * -1;
        } else {
            return 0;
        }
    }
    
    
    /**
     * Get the originInstallationTimestamp and returns it.
     *
     * @param $versionInfoItems []
     *
     * @return int $originInstallationVersionTimestamp
     */
    protected function _getTheOriginInstallationVersionTimestamp(array $versionInfoItems)
    {
        $originInstallationVersionTimestamp = 0;
        
        /** @var VersionInfoItemInterface $versionInfoItem */
        foreach ($versionInfoItems as $versionInfoItem) {
            if ($this->_isSpecialName($versionInfoItem)) {
                continue;
            }
            
            if ($this->_isSpecialNameWithRightVendor($versionInfoItem)
                && $versionInfoItem->isInstalledTargetVersion()) {
                if ($originInstallationVersionTimestamp == 0) {
                    $originInstallationVersionTimestamp = $versionInfoItem->getInstallationDate()->getTimestamp();
                } elseif ($originInstallationVersionTimestamp > $versionInfoItem->getInstallationDate()
                        ->getTimestamp()) {
                    $originInstallationVersionTimestamp = $versionInfoItem->getInstallationDate()->getTimestamp();
                }
            }
        }
        
        return $originInstallationVersionTimestamp;
    }
    
    
    /**
     * Marks with the originInstallationTimestamp the originInstallationVersion
     *
     * @param       $versionInfoItems []
     * @param mixed $originInstallationVersionTimestamp
     */
    protected function _markTheOldestTargetVersion(array $versionInfoItems, $originInstallationVersionTimestamp)
    {
        foreach ($versionInfoItems as $versionInfoItem) {
            if ($this->_isSpecialName($versionInfoItem)) {
                continue;
            }
            
            if ($versionInfoItem->isInstalledTargetVersion()
                && (int)$originInstallationVersionTimestamp == $versionInfoItem->getInstallationDate()
                    ->getTimestamp()) {
                
                $versionInfoItem->setOriginInstallationVersion(true);
                break;
            }
        }
    }
    
    
    /**
     * Get the currentInstallationTimestamp and returns it.
     *
     * @param $versionInfoItems []
     *
     * @return int $currentInstallationVersionTimestamp
     */
    protected function _getTheCurrentInstallationVersionTimestamp(array $versionInfoItems)
    {
        $currentInstallationVersionTimestamp = 0;
        
        /** @var VersionInfoItemInterface $versionInfoItem */
        foreach ($versionInfoItems as $versionInfoItem) {
            if ($this->_isSpecialName($versionInfoItem)) {
                continue;
            }
            
            if ($this->_isSpecialNameWithRightVendor($versionInfoItem)) {
                if ($versionInfoItem->isInstalledTargetVersion()) {
                    if ($currentInstallationVersionTimestamp == 0) {
                        $currentInstallationVersionTimestamp = $versionInfoItem->getInstallationDate()->getTimestamp();
                    } elseif ($currentInstallationVersionTimestamp < $versionInfoItem->getInstallationDate()
                            ->getTimestamp()) {
                        $currentInstallationVersionTimestamp = $versionInfoItem->getInstallationDate()->getTimestamp();
                    }
                }
            }
        }
        
        return $currentInstallationVersionTimestamp;
    }
    
    
    /**
     * Marks with the currentInstallationVersionTimestamp the currentInstallationVersion
     *
     * @param       $versionInfoItems []
     * @param mixed $currentInstallationVersionTimestamp
     */
    protected function _markTheNewestTargetVersion(array $versionInfoItems, $currentInstallationVersionTimestamp)
    {
        /** @var $versionInfoItems $versionInfoItem */
        foreach ($versionInfoItems as $versionInfoItem) {
            if ($this->_isSpecialName($versionInfoItem)) {
                continue;
            }
            
            if ($versionInfoItem->isInstalledTargetVersion()
                && (int)$currentInstallationVersionTimestamp == $versionInfoItem->getInstallationDate()
                    ->getTimestamp()) {
                
                $versionInfoItem->setCurrentInstallationVersion(true);
                break;
            }
        }
    }
    
    
    /**
     * Marks all VersionInfoFileItems as failed installation.
     *
     * @param $versionInfoFileItems []
     */
    protected function _markAllVersionFileItemsWithFailed(array $versionInfoFileItems)
    {
        /** @var $versionInfoItems $versionInfoItem */
        foreach ($versionInfoFileItems as $versionInfoFileItem) {
            if ($this->_isSpecialName($versionInfoFileItem)) {
                continue;
            }
            
            if ($this->_isSpecialNameWithRightVendor($versionInfoFileItem)
                && $versionInfoFileItem->isInstalledTargetVersion() === false) {
                $versionInfoFileItem->setFailedInstallationVersion(true);
            }
        }
    }
    
    
    /**
     * If the Item is an shop update, marks all VersionInfoFileItem under version 2.1 as Target Version .
     *
     * @param $versionInfoFileItems []
     */
    protected function _markOldVersionsCaseAsTargetVersions($versionInfoFileItems)
    {
        /** @var VersionInfoItemInterface $versionInfoFileItem */
        foreach ($versionInfoFileItems as $versionInfoFileItem) {
            if ($this->_isSpecialName($versionInfoFileItem)) {
                continue;
            }
            
            if ($this->_isSpecialNameWithRightVendor($versionInfoFileItem)) {
                if ($versionInfoFileItem->getVersion() !== ' ') {
                    $versionCompare = explode('.', $versionInfoFileItem->getVersion());
                    
                    if ($versionCompare[0] < 2 || ($versionCompare[0] == 2 && $versionCompare[1] == 0)) {
                        $versionInfoFileItem->setIsInstalledTargetVersion(true);
                    }
                }
            }
        }
    }
    
    
    /**
     * Marks all VersionInfoDatabaseItems and VersionInfoFileItems form Gambio, with the same version as
     * InstalledTargetVersion.
     *
     * @param array $versionInfoFileItems     []
     * @param array $versionInfoDatabaseItems []
     */
    protected function _markInstalledTargetVersions(array $versionInfoFileItems, array $versionInfoDatabaseItems)
    {
        foreach ($versionInfoDatabaseItems as $versionInfoDatabaseItem) {
            /** @var VersionInfoItemInterface $versionInfoFileItem */
            foreach ($versionInfoFileItems as $versionInfoFileItem) {
                if ($versionInfoFileItem->getVendor() === 'Gambio'
                    && $versionInfoFileItem->getVersion() === $versionInfoDatabaseItem->getVersion()) {
                    $versionInfoDatabaseItem->setIsInstalledTargetVersion(true);
                    $versionInfoFileItem->setIsInstalledTargetVersion(true);
                    $versionInfoFileItem->setFailedInstallationVersion(false);
                }
            }
        }
    }
    
    
    /**
     * Marks all Versions with 'vrrl', 'Mobile Template' and 'MobileCandy' in Name as target versions.
     *
     * @param array $versionInfoItems
     */
    protected function _marksSpecialVersionNamesAsTargetVersions(array $versionInfoItems)
    {
        /** @var VersionInfoItemInterface $versionInfoFileItem */
        foreach ($versionInfoItems as $versionInfoItem) {
            if ($this->_isSpecialName($versionInfoItem)) {
                $versionInfoItem->setIsInstalledTargetVersion(true);
            }
        }
    }
    
    
    /**
     * Returns true if the version info item has an special name.
     *
     * @param VersionInfoItemInterface $VersionInfoItem
     *
     * @return bool
     */
    protected function _isSpecialName($VersionInfoItem)
    {
        if (strpos($VersionInfoItem->getName(), 'vrrl') !== false
            || strpos($VersionInfoItem->getName(), 'MobileCandy') !== false
            || strpos($VersionInfoItem->getName(), 'Mobile Template') !== false) {
            return true;
        }
        
        return false;
    }
    
    
    /**
     * Returns true if the version info item has an special name with the right Vendor.
     *
     * @param VersionInfoItemInterface $VersionInfoItem
     *
     * @return bool
     */
    protected function _isSpecialNameWithRightVendor($VersionInfoItem)
    {
        if (($VersionInfoItem->getVendor() === 'Gambio')
            && (preg_match('/v[0-9.]+/', $VersionInfoItem->getName()))
            || (strpos($VersionInfoItem->getName(), 'GX ') !== false)) {
            return true;
        }
        
        return false;
    }
}
