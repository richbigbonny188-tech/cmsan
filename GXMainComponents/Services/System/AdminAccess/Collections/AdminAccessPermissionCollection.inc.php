<?php

/* --------------------------------------------------------------
  AdminAccessPermissionCollection.inc.php 2018-01-22
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2017 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Class AdminAccessPermissionCollection
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Collections
 */
class AdminAccessPermissionCollection extends AbstractCollection
{
    /**
     * Return this collections valid type.
     *
     * This method must be implemented in the child-collection classes.
     *
     * @return string Valid type for permission collection.
     */
    protected function _getValidType()
    {
        return AdminAccessPermissionPresentationInterface::class;
    }
}
