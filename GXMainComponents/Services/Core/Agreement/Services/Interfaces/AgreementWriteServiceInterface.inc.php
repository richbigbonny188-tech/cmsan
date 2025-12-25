<?php
/* --------------------------------------------------------------
   AgreementWriteServiceInterface.inc.inc.php 2018-05-08 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface AgreementWriteServiceInterface
 *
 * @category   Core
 * @package    Agreement
 * @subpackage Interfaces
 */
interface AgreementWriteServiceInterface
{
    /**
     * Returns an agreement entity.
     *
     * @return Agreement
     */
    public function create();
    
    
    /**
     * Returns an agreement customer.
     *
     * @param \StringType             $customerName  Customer name.
     * @param \CustomerEmailInterface $customerEmail Customer email.
     *
     * @return \AgreementCustomer
     */
    public function createCustomer(StringType $customerName, CustomerEmailInterface $customerEmail);
    
    
    /**
     * Stores the provided agreement.
     *
     * @param \AgreementInterface $agreement
     *
     * @return $this|\AgreementWriteServiceInterface Same instance for chained method calls.
     */
    public function store(AgreementInterface $agreement);
    
    
    /**
     * Deletes the provided agreement.
     *
     * @param \AgreementInterface $agreement
     *
     * @return $this|\AgreementWriteServiceInterface Same instance for chained method calls.
     */
    public function delete(AgreementInterface $agreement);
}