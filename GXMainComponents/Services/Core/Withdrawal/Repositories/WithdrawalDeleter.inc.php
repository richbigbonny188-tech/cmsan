<?php
/* --------------------------------------------------------------
   WithdrawalDeleter.inc.php 2017-10-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class WithdrawalDeleter
 *
 * @category   System
 * @package    Withdrawal
 * @subpackage Repositories
 */
class WithdrawalDeleter implements WithdrawalDeleterInterface
{
    /**
     * @var \CI_DB_query_builder
     */
    protected $queryBuilder;
    
    
    /**
     * WithdrawalDeleter constructor.
     *
     * @param \CI_DB_query_builder $queryBuilder
     */
    public function __construct(CI_DB_query_builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }
    
    
    /**
     * Deletes withdrawal entity data in database.
     *
     * @param \WithdrawalInterface $withdrawal Withdrawal entity to be delete.
     *
     * @return $this|\WithdrawalDeleterInterface Same instance for chained method calls.
     */
    public function delete(WithdrawalInterface $withdrawal)
    {
        $this->queryBuilder->delete('withdrawals', ['withdrawal_id' => $withdrawal->getWithdrawalId()]);
        
        return $this;
    }
}