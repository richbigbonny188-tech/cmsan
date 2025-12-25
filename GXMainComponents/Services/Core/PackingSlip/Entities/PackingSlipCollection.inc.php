<?php
/* --------------------------------------------------------------
   PackingSlipCollection.inc.php 2018-05-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class PackingSlipCollection
 *
 * @category   System
 * @package    PackingSlip
 * @subpackage Collections
 *
 * @method PackingSlip[] getArray
 */
class PackingSlipCollection extends AbstractCollection
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
        return 'PackingSlip';
    }
}