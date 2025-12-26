<?php

/* --------------------------------------------------------------
   WithdrawalReaderInterface.inc.php 2017-10-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface WithdrawalReaderInterface
 *
 * @category   System
 * @package    Withdrawal
 * @subpackage Repositories
 */
interface WithdrawalReaderInterface
{
    /**
     * Returns all withdrawal entities as array
     *
     * @return array
     */
    public function getAll();
    
    
    /**
     * Returns withdrawal entity data by the given id.
     *
     * @param \IdType $withdrawalId
     *
     * @return array
     */
    public function getById(IdType $withdrawalId);
}