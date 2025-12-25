<?php

/* --------------------------------------------------------------
   AbstractVersionInfoItem.inc.php 2017-05-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AbstractVersionInfoItem
 *
 * @category    System
 * @package     VersionInfo
 * @subpackage  ValueObjects
 */
abstract class AbstractVersionInfoItem implements VersionInfoItemInterface
{
    /**
     * @var string
     */
    protected $name;
    
    /**
     * @var string
     */
    protected $version;
    
    /**
     * @var string
     */
    protected $vendor;
    
    /**
     * @var string
     */
    protected $changelogVersionURL;
    
    /**
     * Installation date of the originally install full shop version.
     *
     * @var DateTime
     */
    protected $installationDate;
    
    /**
     * @var string
     */
    protected $installedStatus;
    
    /**
     * @var bool
     */
    protected $isInstalledTargetVersion = false;
    
    /**
     *
     * @var bool
     */
    protected $isOriginInstallationVersion = false;
    
    /**
     * @var bool
     */
    protected $isCurrentInstallationVersion = false;
    
    /**
     * @var bool
     */
    protected $isForcedInstallationVersion = false;
    
    /**
     * @var bool
     */
    protected $isFailedInstallationVersion = false;
    
    
    /**
     * AbstractVersionInfoItem constructor.
     *
     * @param NonEmptyStringType $name
     * @param NonEmptyStringType $version
     * @param NonEmptyStringType $vendor
     * @param NonEmptyStringType $changelogVersionURL
     * @param DateTime           $installationDate
     * @param BoolType           $installedStatus
     */
    public function __construct(
        NonEmptyStringType $name,
        NonEmptyStringType $version,
        NonEmptyStringType $vendor,
        NonEmptyStringType $changelogVersionURL,
        DateTime $installationDate,
        BoolType $installedStatus
    ) {
        $this->name                = $name->asString();
        $this->version             = $version->asString();
        $this->vendor              = $vendor->asString();
        $this->changelogVersionURL = $changelogVersionURL->asString();
        $this->installationDate    = $installationDate;
        $this->installedStatus     = $installedStatus->asBool();
    }
    
    
    /**
     * Returns the name of version history item.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    
    /**
     * Returns the version of version history item.
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }
    
    
    /**
     * Returns the installation date of version history item.
     *
     * @return DateTime
     */
    public function getInstallationDate()
    {
        return $this->installationDate;
    }
    
    
    /**
     * Returns the status of the Installation.
     *
     * @return string
     */
    public function getInstalledStatus()
    {
        return $this->installedStatus;
    }
    
    
    /**
     * Mark this version info item as installed target version and returns VersionInfoItemInterface.
     *
     * @param bool $isInstalledTargetVersion
     *
     * @return VersionInfoItemInterface
     */
    public function setIsInstalledTargetVersion($isInstalledTargetVersion)
    {
        $this->isInstalledTargetVersion = (bool)$isInstalledTargetVersion;
        
        return $this;
    }
    
    
    /**
     * Returns true if this version info item is an installed target version.
     *
     * @return bool
     */
    public function isInstalledTargetVersion()
    {
        return $this->isInstalledTargetVersion;
    }
    
    
    /**
     * Returns the vendor of version info item.
     *
     * @return string
     */
    public function getVendor()
    {
        return $this->vendor;
    }
    
    
    /**
     * Returns the changelog url of version info item.
     *
     * @return string
     */
    public function getChangelogVersionURL()
    {
        return $this->changelogVersionURL;
    }
    
    
    /**
     * Mark this version info item as origin installation version and returns VersionInfoItemInterface.
     *
     * @param $isOriginInstallationVersion
     *
     * @return VersionInfoItemInterface
     */
    public function setOriginInstallationVersion($isOriginInstallationVersion)
    {
        $this->isOriginInstallationVersion = (bool)$isOriginInstallationVersion;
        
        return $this;
    }
    
    
    /**
     * Returns true if target version is the oldest one.
     *
     * @return bool
     */
    public function isOriginInstallationVersion()
    {
        return $this->isOriginInstallationVersion;
    }
    
    
    /**
     * Mark this version info item as Current target version and returns VersionInfoItemInterface.
     *
     * @param bool $isCurrentInstallationVersion
     *
     * @return VersionInfoItemInterface
     */
    public function setCurrentInstallationVersion($isCurrentInstallationVersion)
    {
        $this->isCurrentInstallationVersion = (bool)$isCurrentInstallationVersion;
        
        return $this;
    }
    
    
    /**
     * Returns true if target version is the oldest one.
     *
     * @return bool
     */
    public function isCurrentInstallationVersion()
    {
        return $this->isCurrentInstallationVersion;
    }
    
    
    /**
     * Mark this version info item as forced installation version and returns VersionInfoItemInterface.
     *
     * @return bool
     */
    public function isForcedInstallationVersion()
    {
        return $this->isForcedInstallationVersion;
    }
    
    
    /**
     * Returns true if installation version is a forced installation.
     *
     * @param bool $isForcedInstallationVersion
     *
     * @return VersionInfoItemInterface
     */
    public function setForcedInstallationVersion($isForcedInstallationVersion)
    {
        $this->isForcedInstallationVersion = (bool)$isForcedInstallationVersion;
        
        return $this;
    }
    
    
    /**
     * Mark this version info item as failed installation version and returns VersionInfoItemInterface.
     *
     * @return bool
     */
    public function isFailedInstallationVersion()
    {
        return $this->isFailedInstallationVersion;
    }
    
    
    /**
     * Returns true if installation version is a failed installation.
     *
     * @param bool $isFailedInstallationVersion
     *
     * @return VersionInfoItemInterface
     */
    public function setFailedInstallationVersion($isFailedInstallationVersion)
    {
        $this->isFailedInstallationVersion = (bool)$isFailedInstallationVersion;
        
        return $this;
    }
}