<?php

/* --------------------------------------------------------------
   InvoiceListGeneratorInterface.inc.php 2016-10-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface InvoiceListGeneratorInterface
 *
 * @category   System
 * @package    Invoice
 * @subpackage Interfaces
 */
interface InvoiceListGeneratorInterface
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
     * Filter invoice list items by the provided parameters.
     *
     * The following slug names need to be used:
     *
     *   - invoiceNumber => invoices.invoice_number
     *   - invoiceDate => invoices.invoice_date
     *   - sum => invoices.total_sum
     *   - customer => invoices.billing_firstname invoices.billing_lastname
     *   - group => invoices.customer_status_name
     *   - countryIsoCode => invoices.billing_country_iso_code_2
     *   - orderNumber => invoices.order_id
     *   - orderDate => invoices.order_date_purchased
     *   - paymentMethod => invoices.payment_class
     *   - status => orders_status.orders_status_name
     *
     * @param array           $filterParameters Contains the column slug-names and their values.
     * @param IntType|null    $startIndex       The start index of the wanted array to be returned (default = null).
     * @param IntType|null    $maxCount         Maximum amount of items which should be returned (default = null).
     * @param StringType|null $orderBy          A string which defines how the items should be ordered (default = null).
     *
     * @return InvoiceListItemCollection
     *
     * @throws BadMethodCallException
     * @throws InvalidArgumentException
     */
    public function filterInvoiceList(
        array $filterParameters,
        IntType $startIndex = null,
        IntType $maxCount = null,
        StringType $orderBy = null
    );
    
    
    /**
     * Get the filtered invoice count.
     *
     * This number is useful for pagination functionality where the app needs to know the number of the filtered rows.
     *
     * @param array $filterParameters
     *
     * @return int
     *
     * @throws BadMethodCallException
     */
    public function filterInvoiceListCount(array $filterParameters);
}