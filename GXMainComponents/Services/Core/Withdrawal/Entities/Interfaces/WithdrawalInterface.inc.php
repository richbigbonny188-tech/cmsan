<?php
/* --------------------------------------------------------------
   WithdrawalInterface.inc.php 2017-10-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface WithdrawalInterface
 *
 * @category   System
 * @package    Withdrawal
 * @subpackage Entities
 */
interface WithdrawalInterface
{
    /**
     * Saves withdrawal in database.
     *
     * @return $this|\WithdrawalInterface Same instance for chained method calls.
     */
    public function store();
    
    
    /**
     * Deletes withdrawal from database.
     *
     * @return $this|\WithdrawalInterface Same instance for chained method calls.
     */
    public function delete();
    
    
    /**
     * Returns the withdrawal id.
     *
     * @return int Withdrawal id.
     */
    public function getWithdrawalId();
    
    
    /**
     * Sets the withdrawal id.
     *
     * @param \IdType $withdrawalId The withdrawal id to be set.
     *
     * @return $this|\WithdrawalInterface Same instance for chained method calls.
     */
    public function setWithdrawalId(IdType $withdrawalId);
    
    
    /**
     * Returns the withdrawal datetime.
     *
     * @return \DateTime
     */
    public function getWithdrawalDate();
    
    
    /**
     * sets the datetime.
     *
     * @param \DateTime $withdrawalDate Date to be set.
     *
     * @return $this|\WithdrawalInterface Same instance for chained method calls.
     */
    public function setWithdrawalDate(\DateTime $withdrawalDate);
    
    
    /**
     * Returns the content.
     *
     * @return string
     */
    public function getWithdrawalContent();
    
    
    /**
     * Sets the withdrawal content.
     *
     * @param \StringType $withdrawalContent Withdrawal content to be set.
     *
     * @return $this|\WithdrawalInterface Same instance for chained method calls.
     */
    public function setWithdrawalContent(StringType $withdrawalContent);
    
    
    /**
     * Returns the withdrawal created datetime.
     *
     * @return \DateTime
     */
    public function getDateCreated();
    
    
    /**
     * Sets the withdrawal created datetime.
     *
     * @param \DateTime $dateCreated Created date to be set.
     *
     * @return $this|\WithdrawalInterface Same instance for chained method calls.
     */
    public function setDateCreated(\DateTime $dateCreated);
    
    
    /**
     * Checks if withdrawal by admin created or not.
     *
     * @return bool
     */
    public function getCreatedByAdmin();
    
    
    /**
     * Sets whether the withdrawal by admin created or not.
     *
     * @param \BoolType $createdByAdmin
     *
     * @return $this|\WithdrawalInterface Same instance for chained method calls.
     */
    public function setCreatedByAdmin(BoolType $createdByAdmin);
    
    
    /**
     * Returns the withdrawal Order.
     *
     * @return \WithdrawalOrderInterface
     */
    public function getWithdrawalOrder();
    
    
    /**
     * Sets the withdrawal order.
     *
     * @param \WithdrawalOrderInterface $withdrawalOrder
     *
     * @return $this|\WithdrawalInterface Same instance for chained method calls.
     */
    public function setWithdrawalOrder(WithdrawalOrderInterface $withdrawalOrder);
}