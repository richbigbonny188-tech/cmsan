<?php

/* --------------------------------------------------------------
   VersionInfoItemInterface.inc.php 2017-05-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class VersionInfoItemInterface
 *
 * @category    System
 * @package     VersionInfo
 * @subpackage  ValueObjects
 */
interface VersionInfoItemInterface
{
    /**
     * Returns the name of version history item.
     *
     * @return string
     */
    public function getName();
    
    
    /**
     * Returns the version of version history item.
     *
     * @return string
     */
    public function getVersion();
    
    
    /**
     * Returns the installation date of version history item.
     *
     * @return DateTime
     */
    public function getInstallationDate();
    
    
    /**
     * Returns the status of the Installation.
     *
     * @return string
     */
    public function getInstalledStatus();
    
    
    /**
     * Mark this version info item as installed target version and returns VersionInfoItemInterface .
     *
     * @param bool $isInstalledTargetVersion
     *
     * @return VersionInfoItemInterface
     */
    public function setIsInstalledTargetVersion($isInstalledTargetVersion);
    
    
    /**
     * Returns true if this version info item is an installed target version.
     *
     * @return bool
     */
    public function isInstalledTargetVersion();
    
    
    /**
     * Returns the vendor of version info item.
     *
     * @return string
     */
    public function getVendor();
    
    
    /**
     * Returns the changelog url of version info item.
     *
     * @return string
     */
    public function getChangelogVersionURL();
    
    
    /**
     * Mark this version info item as origin installation version and returns VersionInfoItemInterface.
     *
     * @param $isOriginInstallationVersion
     *
     * @return VersionInfoItemInterface
     */
    public function setOriginInstallationVersion($isOriginInstallationVersion);
    
    
    /**
     * Returns true if installation version is the oldest one.
     *
     * @return bool
     */
    public function isOriginInstallationVersion();
    
    
    /**
     * Mark this version info item as current installation version and returns VersionInfoItemInterface.
     *
     * @param bool $isCurrentInstallationVersion
     *
     * @return VersionInfoItemInterface
     */
    public function setCurrentInstallationVersion($isCurrentInstallationVersion);
    
    
    /**
     * Returns true if installation version is the oldest one.
     *
     * @return bool
     */
    public function isCurrentInstallationVersion();
    
    
    /**
     * Mark this version info item as forced installation version and returns VersionInfoItemInterface.
     *
     * @param $isForcedInstallationVersion
     *
     * @return bool
     */
    public function setForcedInstallationVersion($isForcedInstallationVersion);
    
    
    /**
     * Returns true if installation version is a forced installation.
     *
     * @return bool
     */
    public function isForcedInstallationVersion();
    
    
    /**
     * Mark this version info item as failed installation version and returns VersionInfoItemInterface.
     *
     * @param $isFailedInstallationVersion
     *
     * @return bool
     */
    public function setFailedInstallationVersion($isFailedInstallationVersion);
    
    
    /**
     * Returns true if installation version is a failed installation.
     *
     * @return bool
     */
    public function isFailedInstallationVersion();
}