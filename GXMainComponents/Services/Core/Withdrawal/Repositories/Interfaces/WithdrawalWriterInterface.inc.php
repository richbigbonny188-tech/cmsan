<?php
/* --------------------------------------------------------------
   WithdrawalWriterInterface.inc.php 2017-10-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface WithdrawalWriterInterface
 *
 * @category   System
 * @package    Withdrawal
 * @subpackage Repositories
 */
interface WithdrawalWriterInterface
{
    /**
     * Saves withdrawal entity data in database.
     *
     * @param \WithdrawalInterface $withdrawal Withdrawal to be saved.
     *
     * @return $this|\WithdrawalWriterInterface Same instance for chained method calls.
     */
    public function store(WithdrawalInterface $withdrawal);
    
    
    /**
     * Updates withdrawal entity data in database.
     *
     * @param \WithdrawalInterface $withdrawal Withdrawal to be updated.
     *
     * @return $this|\WithdrawalWriterInterface Same instance for chained method calls.
     */
    public function update(WithdrawalInterface $withdrawal);
}