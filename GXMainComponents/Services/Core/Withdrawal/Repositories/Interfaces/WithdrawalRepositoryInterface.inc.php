<?php
/* --------------------------------------------------------------
   WithdrawalRepositoryInterface.inc.php 2017-10-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface WithdrawalRepositoryInterface
 *
 * @category   System
 * @package    Withdrawal
 * @subpackage Repositories
 */
interface WithdrawalRepositoryInterface
{
    /**
     * Saves withdrawal entity in database.
     *
     * @param \WithdrawalInterface $withdrawal Withdrawal entity to be saved.
     *
     * @return $this|\WithdrawalRepositoryInterface Same instance for chained method calls.
     */
    public function store(WithdrawalInterface $withdrawal);
    
    
    /**
     * Deletes withdrawal entity from database
     *
     * @param \WithdrawalInterface $withdrawal Withdrawal entity to be deleted.
     *
     * @return $this|\WithdrawalRepositoryInterface Same instance for chained method calls.
     */
    public function delete(WithdrawalInterface $withdrawal);
}