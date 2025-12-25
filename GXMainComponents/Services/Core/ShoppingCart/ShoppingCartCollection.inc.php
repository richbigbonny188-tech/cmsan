<?php
/* --------------------------------------------------------------
   ShoppingCartCollection.inc.php 2018-05-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ShoppingCartCollection
 *
 * @category   System
 * @package    ShoppingCart
 * @subpackage Entities
 */
class ShoppingCartCollection extends EditableCollection
{
    /**
     * Valid type for ShoppingCartCollection is ShoppingCartInterface.
     *
     * @return string
     */
    public function _getValidType()
    {
        return 'ShoppingCartInterface';
    }
}