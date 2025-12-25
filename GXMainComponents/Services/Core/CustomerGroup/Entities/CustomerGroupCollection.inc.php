<?php
/* --------------------------------------------------------------
   CustomerGroupCollection.inc.php 2017-09-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CustomerGroupCollection
 *
 * @category   System
 * @package    CustomerGroup
 * @subpackage Entities
 */
class CustomerGroupCollection extends EditableCollection
{
    /**
     * Valid type for CustomerGroupCollection is CustomerGroupInterface.
     *
     * @return string
     */
    public function _getValidType()
    {
        return \CustomerGroupInterface::class;
    }
}