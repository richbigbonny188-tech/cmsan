<?php
/* --------------------------------------------------------------
   WithdrawalCollection.inc.php 2017-10-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class WithdrawalCollection
 *
 * @category   System
 * @package    Withdrawal
 * @subpackage Entities
 */
class WithdrawalCollection extends EditableCollection
{
    /**
     * Valid type for WithdrawalCollection is WithdrawalInterface.
     *
     * @return string
     */
    public function _getValidType()
    {
        return '\WithdrawalInterface';
    }
}