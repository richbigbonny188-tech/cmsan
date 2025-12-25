<?php

/* --------------------------------------------------------------
   OrderStatusCollection.inc.php 2017-03-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class OrderStatusCollection
 *
 * @category   System
 * @package    OrderStatus
 * @subpackage Collections
 *
 * @method OrderStatus[] getArray
 */
class OrderStatusCollection extends AbstractCollection
{
    /**
     * Get valid type.
     *
     * This method must be implemented in the child-collection classes.
     *
     * @return string
     */
    protected function _getValidType()
    {
        return 'OrderStatus';
    }
}