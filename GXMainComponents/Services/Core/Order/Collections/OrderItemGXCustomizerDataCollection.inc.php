<?php
/* --------------------------------------------------------------
   OrderItemGXCustomizerDataCollection.inc.php 2017-10-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractCollection');

/**
 * Class OrderItemGXCustomizerDataCollection
 *
 * @category   System
 * @package    Order
 * @subpackage Collections
 */
class OrderItemGXCustomizerDataCollection extends AbstractCollection
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
        return 'OrderItemGXCustomizerData';
    }
}