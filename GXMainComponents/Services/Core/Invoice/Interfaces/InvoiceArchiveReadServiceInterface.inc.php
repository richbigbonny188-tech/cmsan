<?php
/* --------------------------------------------------------------
   InvoiceArchiveReadServiceInterface.inc.php 2016-10-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface InvoiceFileEntitlerInterface
 *
 * @category   System
 * @package    Invoice
 * @subpackage Interfaces
 */
interface InvoiceArchiveReadServiceInterface
{
    /**
     * Returns an invoice list item collection by the given conditions.
     * The other arguments helps to control fetched data.
     *
     * @param array           $conditions (Optional) Conditions for tht where clause.
     * @param IntType|null    $startIndex (Optional) Start index for the limit clause.
     * @param IntType|null    $maxCount   (Optional) Max count for the limit clause.
     * @param StringType|null $orderBy    (Optional) Sort order of fetched data.
     *
     * @return InvoiceListItemCollection
     */
    public function getInvoiceListByConditions(
        array $conditions = [],
        IntType $startIndex = null,
        IntType $maxCount = null,
        StringType $orderBy = null
    );
    
    
    /**
     * Returns the invoice file download information.
     *
     * @param IdType $invoiceId invoice_id of expected entry.
     *
     * @return FileDownloadInformation
     */
    public function getInvoiceFileDownloadInfoByInvoiceId(IdType $invoiceId);
    
    
    /**
     * Returns the invoice to the given id
     *
     * @param IdType $invoiceId
     *
     * @return InvoiceListItem
     * @throws UnexpectedValueException if invoice does not exist
     *
     */
    public function getInvoiceListItemById(IdType $invoiceId);
    
    
    /**
     * Filter the invoice records with specific conditions.
     *
     * Provide the filtering values in the conditions array in order to fetch a filtered result set.
     *
     * @param array      $filterParameters Contains an array of the GET parameters to be used for filtering the order
     *                                     records.
     * @param IntType    $startIndex       Start index of order list item collections which should be returned.
     * @param IntType    $maxCount         Maximum amount of collections.
     * @param StringType $orderBy          Argument to specify the order.
     *
     * @return InvoiceListItemCollection
     */
    public function filterInvoiceList(
        array $filterParameters,
        IntType $startIndex = null,
        IntType $maxCount = null,
        StringType $orderBy = null
    );
    
    
    /**
     * Get the filtered invoices count.
     *
     * @param array $filterParameters
     *
     * @return int
     */
    public function filterInvoiceListCount(array $filterParameters);
}
