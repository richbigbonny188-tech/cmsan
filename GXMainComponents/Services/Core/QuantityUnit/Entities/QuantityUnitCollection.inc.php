<?php

/* --------------------------------------------------------------
   QuantityUnitCollection.inc.php 2017-08-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class QuantityUnitCollection
 *
 * @category   System
 * @package    QuantityUnit
 * @subpackage Entities
 */
class QuantityUnitCollection extends EditableCollection
{
    /**
     * Get valid type.
     *
     * @return string
     */
    public function _getValidType()
    {
        return '\QuantityUnitInterface';
    }
}