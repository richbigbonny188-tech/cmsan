<?php
/* --------------------------------------------------------------
   InvoiceRepository.inc.php 2016-10-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class InvoiceRepository
 *
 * @category   System
 * @package    Invoice
 */
class InvoiceRepository implements InvoiceRepositoryInterface
{
    /**
     * @var CI_DB_query_builder
     */
    protected $db;
    
    /**
     * @var string
     */
    protected $tableName = 'invoices';
    
    
    /**
     * InvoiceRepository constructor.
     *
     * @param CI_DB_query_builder $db
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * Adds a new invoice in the database.
     *
     * @param InvoiceInformation $invoiceInfo Entity with invoice information.
     *
     * @return int The invoice_id of the new database entry.
     */
    public function add(InvoiceInformation $invoiceInfo)
    {
        $customerData = $this->db->get_where('orders', ['orders_id' => $invoiceInfo->getOrderId()])->row_array();
        if ($customerData === null) {
            $customerData = $this->db->get_where('customers',
                                                 ['customers_id' => $invoiceInfo->getCustomerId()])->row_array();
        }
        
        $invoiceData = [
            'invoice_number'              => $invoiceInfo->getInvoiceNumber(),
            'invoice_date'                => $invoiceInfo->getInvoiceDate()->format('Y-m-d H:i:s'),
            'customer_id'                 => $customerData['customers_id'] ? : '0',
            'customer_status_id'          => $invoiceInfo->getCustomerStatusInformation()->getStatusId(),
            'customer_status_name'        => $invoiceInfo->getCustomerStatusInformation()->getStatusName(),
            'total_sum'                   => $invoiceInfo->getTotalSum(),
            'currency'                    => $invoiceInfo->getCurrency()->getCode(),
            'customers_firstname'         => $customerData['customers_firstname'] ? : '',
            'customers_lastname'          => $customerData['customers_lastname'] ? : '',
            'delivery_firstname'          => $invoiceInfo->getShippingAddress()->getFirstname(),
            'delivery_lastname'           => $invoiceInfo->getShippingAddress()->getLastname(),
            'delivery_company'            => $invoiceInfo->getShippingAddress()->getCompany(),
            'delivery_street_address'     => $invoiceInfo->getShippingAddress()->getStreet(),
            'delivery_house_number'       => $invoiceInfo->getShippingAddress()->getHouseNumber(),
            'delivery_additional_info'    => $invoiceInfo->getShippingAddress()->getAdditionalAddressInfo(),
            'delivery_suburb'             => $invoiceInfo->getShippingAddress()->getSuburb(),
            'delivery_city'               => $invoiceInfo->getShippingAddress()->getCity(),
            'delivery_postcode'           => $invoiceInfo->getShippingAddress()->getPostcode(),
            'delivery_state'              => $invoiceInfo->getShippingAddress()->getCountryZone()->getName(),
            'delivery_country'            => $invoiceInfo->getShippingAddress()->getCountry()->getName(),
            'delivery_country_iso_code_2' => $invoiceInfo->getShippingAddress()->getCountry()->getIso2(),
            'delivery_address_format_id'  => $invoiceInfo->getShippingAddress()->getCountry()->getAddressFormatId(),
            'billing_firstname'           => $invoiceInfo->getPaymentAddress()->getFirstname(),
            'billing_lastname'            => $invoiceInfo->getPaymentAddress()->getLastname(),
            'billing_company'             => $invoiceInfo->getPaymentAddress()->getCompany(),
            'billing_street_address'      => $invoiceInfo->getPaymentAddress()->getStreet(),
            'billing_house_number'        => $invoiceInfo->getPaymentAddress()->getHouseNumber(),
            'billing_additional_info'     => $invoiceInfo->getPaymentAddress()->getAdditionalAddressInfo(),
            'billing_suburb'              => $invoiceInfo->getPaymentAddress()->getSuburb(),
            'billing_city'                => $invoiceInfo->getPaymentAddress()->getCity(),
            'billing_postcode'            => $invoiceInfo->getPaymentAddress()->getPostcode(),
            'billing_state'               => $invoiceInfo->getPaymentAddress()->getCountryZone()->getName(),
            'billing_country'             => $invoiceInfo->getPaymentAddress()->getCountry()->getName(),
            'billing_country_iso_code_2'  => $invoiceInfo->getPaymentAddress()->getCountry()->getIso2(),
            'billing_address_format_id'   => $invoiceInfo->getPaymentAddress()->getCountry()->getAddressFormatId(),
            'order_id'                    => $invoiceInfo->getOrderId(),
            'order_date_purchased'        => $invoiceInfo->getOrderPurchaseDate()->format('Y-m-d H:i:s'),
            'payment_method'              => $invoiceInfo->getPaymentType()->getTitle(),
            'payment_class'               => $invoiceInfo->getPaymentType()->getModule()
        ];
        
        $this->db->insert($this->tableName, $invoiceData);
        
        return $this->db->insert_id();
    }
    
    
    /**
     * Updates the invoice file column for an invoice entry in the database.
     *
     * @param IdType             $invoiceId
     * @param FilenameStringType $invoiceFilename
     *
     * @return $this|InvoiceRepository Same instance for chained method calls.
     */
    public function updateInvoiceFilename(IdType $invoiceId, FilenameStringType $invoiceFilename)
    {
        $this->db->update($this->tableName,
                          ['invoice_file' => $invoiceFilename->asString()],
                          ['invoice_id' => $invoiceId->asInt()]);
        
        return $this;
    }
    
    
    /**
     * Removes an invoice entry from the database.
     *
     * @param IdType $invoiceId invoice_id of entry to be removed.
     *
     * @return $this|InvoiceRepository Same instance for chained method calls.
     */
    public function deleteByInvoiceId(IdType $invoiceId)
    {
        $this->db->delete($this->tableName, ['invoice_id' => $invoiceId->asInt()]);
        
        return $this;
    }
}