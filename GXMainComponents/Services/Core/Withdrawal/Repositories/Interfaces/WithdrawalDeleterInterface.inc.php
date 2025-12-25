<?php
/* --------------------------------------------------------------
   WithdrawalDeleterInterface.inc.php 2017-10-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface WithdrawalDeleterInterface
 *
 * @category   System
 * @package    Withdrawal
 * @subpackage Repositories
 */
interface WithdrawalDeleterInterface
{
    /**
     * Deletes withdrawal entity data in database.
     *
     * @param \WithdrawalInterface $withdrawal Withdrawal entity to be delete.
     *
     * @return $this|\WithdrawalDeleterInterface Same instance for chained method calls.
     */
    public function delete(WithdrawalInterface $withdrawal);
    
}