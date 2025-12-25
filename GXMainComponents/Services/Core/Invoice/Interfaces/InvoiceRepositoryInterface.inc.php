<?php

/* --------------------------------------------------------------
   InvoiceRepositoryInterface.inc.php 2016-10-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface InvoiceRepositoryInterface
 *
 * @category   System
 * @package    Invoice
 * @subpackage Interfaces
 */
interface InvoiceRepositoryInterface
{
    /**
     * Adds a new invoice in the database.
     *
     * @param InvoiceInformation $invoiceInfo Entity with invoice information.
     *
     * @return int The invoice_id of the new database entry.
     */
    public function add(InvoiceInformation $invoiceInfo);
    
    
    /**
     * Updates the invoice file column for an invoice entry in the database.
     *
     * @param IdType             $invoiceId
     * @param FilenameStringType $invoiceFilename
     *
     * @return $this|InvoiceRepositoryInterface Same instance for chained method calls.
     */
    public function updateInvoiceFilename(IdType $invoiceId, FilenameStringType $invoiceFilename);
    
    
    /**
     * Removes an invoice entry from the database.
     *
     * @param IdType $invoiceId invoice_id of entry to be removed.
     *
     * @return $this|InvoiceRepositoryInterface Same instance for chained method calls.
     */
    public function deleteByInvoiceId(IdType $invoiceId);
}