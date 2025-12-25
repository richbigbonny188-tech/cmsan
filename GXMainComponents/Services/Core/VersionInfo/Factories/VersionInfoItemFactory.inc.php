<?php
/* --------------------------------------------------------------
   VersionInfoItemFactory.inc.php 2017-05-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class VersionInfoItemFactory
 *
 * @category   Core
 * @package    VersionInfo
 * @subpackage Factories
 */
class VersionInfoItemFactory
{
    /**
     * Create new VersionInfoDatabaseItem object.
     *
     * @param NonEmptyStringType $name
     * @param NonEmptyStringType $version
     * @param NonEmptyStringType $vendor
     * @param NonEmptyStringType $changelogVersionURL
     * @param DateTime           $installationDate
     * @param BoolType           $installedStatus
     *
     * @return VersionInfoDatabaseItem
     */
    public function createVersionInfoDatabaseItem(
        NonEmptyStringType $name,
        NonEmptyStringType $version,
        NonEmptyStringType $vendor,
        NonEmptyStringType $changelogVersionURL,
        DateTime $installationDate,
        BoolType $installedStatus
    ) {
        return new VersionInfoDatabaseItem($name,
                                           $version,
                                           $vendor,
                                           $changelogVersionURL,
                                           $installationDate,
                                           $installedStatus);
    }
    
    
    /**
     * Create new VersionInfoFileItem object.
     *
     * @param NonEmptyStringType $name
     * @param NonEmptyStringType $version
     * @param NonEmptyStringType $vendor
     * @param NonEmptyStringType $changelogVersionURL
     * @param DateTime           $installationDate
     * @param BoolType           $installedStatus
     *
     * @return VersionInfoFileItem
     */
    public function createVersionInfoFileItem(
        NonEmptyStringType $name,
        NonEmptyStringType $version,
        NonEmptyStringType $vendor,
        NonEmptyStringType $changelogVersionURL,
        DateTime $installationDate,
        BoolType $installedStatus
    ) {
        return new VersionInfoFileItem($name,
                                       $version,
                                       $vendor,
                                       $changelogVersionURL,
                                       $installationDate,
                                       $installedStatus);
    }
}