<?php

/* --------------------------------------------------------------
    AdminAccessPermissionInterface.inc.php 2017-12-20
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2017 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------
*/

/**
 * Interface AdminAccessPermissionInterface
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Entities
 */
interface AdminAccessPermissionInterface
{
    /**
     * AdminAccessPermission constructor.
     *
     * @param AdminAccessPermissionDeleterInterface $permissionDeleter Permission deleter.
     * @param AdminAccessPermissionWriterInterface  $permissionWriter  Permission writer.
     * @param AdminAccessGroupReaderInterface       $groupReader       Group reader.
     * @param AdminAccessPermissionReaderInterface  $permissionReader  Permission reader.
     */
    public function __construct(
        AdminAccessPermissionDeleterInterface $permissionDeleter,
        AdminAccessPermissionWriterInterface $permissionWriter,
        AdminAccessGroupReaderInterface $groupReader,
        AdminAccessPermissionReaderInterface $permissionReader
    );
}
