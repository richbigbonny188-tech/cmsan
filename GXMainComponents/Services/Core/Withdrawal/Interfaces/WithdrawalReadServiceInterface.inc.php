<?php
/* --------------------------------------------------------------
   WithdrawalReadServiceInterface.inc.php 2018-01-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface WithdrawalReadServiceInterface
 *
 * @category   System
 * @package    Withdrawal
 * @subpackage Interfaces
 */
interface WithdrawalReadServiceInterface
{
    /**
     * Returns withdrawal entities as collection.
     *
     * @return \WithdrawalCollection
     */
    public function getAll();
    
    
    /**
     * Returns withdrawal entity by given id.
     *
     * @param \IdType $withdrawalId
     *
     * @return \Withdrawal
     */
    public function getById(IdType $withdrawalId);
}
