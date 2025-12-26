<?php

/* --------------------------------------------------------------
   WithdrawalRepository.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class WithdrawalRepository
 *
 * @category   System
 * @package    Withdrawal
 * @subpackage Repositories
 */
class WithdrawalRepository implements WithdrawalRepositoryInterface
{
    /**
     * @var \WithdrawalWriterInterface
     */
    protected $writer;
    
    /**
     * @var \WithdrawalDeleterInterface
     */
    protected $deleter;
    
    
    /**
     * WithdrawalRepository constructor.
     *
     * @param \WithdrawalWriterInterface  $writer
     * @param \WithdrawalDeleterInterface $deleter
     */
    public function __construct(WithdrawalWriterInterface $writer, WithdrawalDeleterInterface $deleter)
    {
        $this->writer  = $writer;
        $this->deleter = $deleter;
    }
    
    
    /**
     * Saves withdrawal entity in database.
     *
     * @param \WithdrawalInterface $withdrawal Withdrawal entity to be saved.
     *
     * @return $this|\WithdrawalRepositoryInterface Same instance for chained method calls.
     */
    public function store(WithdrawalInterface $withdrawal)
    {
        if ($withdrawal->getWithdrawalId() === 0) {
            $this->writer->store($withdrawal);
        } else {
            $this->writer->update($withdrawal);
        }
        
        return $this;
    }
    
    
    /**
     * Deletes withdrawal entity from database
     *
     * @param \WithdrawalInterface $withdrawal Withdrawal entity to be deleted.
     *
     * @return $this|\WithdrawalRepositoryInterface Same instance for chained method calls.
     */
    public function delete(WithdrawalInterface $withdrawal)
    {
        $this->deleter->delete($withdrawal);
        
        return $this;
    }
}