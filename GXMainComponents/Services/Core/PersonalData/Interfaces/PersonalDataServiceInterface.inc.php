<?php
/* --------------------------------------------------------------
   PersonalDataServiceInterface.inc.php 2018-05-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface PersonalDataServiceInterface
 */
interface PersonalDataServiceInterface
{
    /**
     * Deletes all data for a given customer. The following personal data will be deleted:
     *
     * - base data
     * - addresses
     * - orders
     * - invoices
     * - packing slips
     * - withdrawals
     * - agreements
     * - emails
     * - shopping carts
     * - shared shopping carts
     * - reviews
     * - newsletter subscriptions
     *
     * @param IdType                $customerId
     * @param PersonalDataSelection $selection Personal data selection
     *
     * @throws InvalidArgumentException
     */
    public function deletePersonalDataByCustomerId(IdType $customerId, PersonalDataSelection $selection);
    
    
    /**
     * Exports all personal data of a given customer including the following data:
     *
     * - base data
     * - addresses
     * - orders
     * - invoices
     * - packing slips
     * - withdrawals
     * - agreements
     * - emails
     * - shopping carts
     * - shared shopping carts
     * - reviews
     * - newsletter subscriptions
     *
     * @param \IdType               $customerId
     * @param PersonalDataSelection $selection Personal data selection
     */
    public function exportPersonalDataByCustomerId(IdType $customerId, PersonalDataSelection $selection);
    
    
    /**
     * @param \InvoiceArchiveReadServiceInterface $invoiceArchiveReadService
     */
    public function setInvoiceArchiveReadService(InvoiceArchiveReadServiceInterface $invoiceArchiveReadService);
    
    
    /**
     * @param \InvoiceArchiveWriteServiceInterface $invoiceArchiveWriteService
     */
    public function setInvoiceArchiveWriteService(InvoiceArchiveWriteServiceInterface $invoiceArchiveWriteService);
    
    
    /**
     * @param \PackingSlipServiceInterface $packingSlipService
     */
    public function setPackingSlipService(PackingSlipServiceInterface $packingSlipService);
}