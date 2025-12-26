<?php
/* --------------------------------------------------------------
   WithdrawalAccessRepositoryInterface.inc.php 2018-01-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface WithdrawalAccessRepositoryInterface
 *
 * @category   System
 * @package    Withdrawal
 * @subpackage Repositories
 */
interface WithdrawalAccessRepositoryInterface
{
    /**
     * Returns all withdrawal as collection.
     *
     * @return WithdrawalCollection
     */
    public function getAll();
    
    
    /**
     * Returns withdrawal entity by given id.
     *
     * @param \IdType $withdrawalId IdType of entity to be returned.
     *
     * @return \Withdrawal
     */
    public function getById(IdType $withdrawalId);
}
