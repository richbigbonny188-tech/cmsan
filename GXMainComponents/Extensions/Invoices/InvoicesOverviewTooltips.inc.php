<?php

/* --------------------------------------------------------------
   InvoicesOverviewTooltips.inc.php 2022-08-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class InvoicesOverviewTooltips
 *
 * This class generates the required HTML for the tooltips of each row in the invoices overview table.
 * In order to be faster do not use any services but fetch the data directly with DB queries.
 *
 * @category   System
 * @package    Extensions
 * @subpackage Invoices
 */
class InvoicesOverviewTooltips
{
    /**
     * @var ContentView
     */
    protected $contentView;
    
    /**
     * @var CI_DB_query_builder
     */
    protected $db;
    
    
    /**
     * InvoicesOverviewTooltips constructor.
     */
    public function __construct()
    {
        $this->db          = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $this->contentView = MainFactory::create('ContentView');
        $this->contentView->set_escape_html(true);
        $this->contentView->set_flat_assigns(true);
        $this->contentView->set_template_dir(DIR_FS_ADMIN . 'html/content/invoices/tooltips/');
    }
    
    
    /**
     * Get the row tooltip HTML for each displayed tooltip.
     *
     * @param InvoiceListItem $invoiceListItem Contains the invoice list item data.
     *
     * @return array
     */
    public function getRowTooltips(InvoiceListItem $invoiceListItem)
    {
        $rowTooltips = [
            'invoiceItems'       => $this->_getInvoiceItems($invoiceListItem),
            'customerMemos'      => $this->_getCustomerMemos($invoiceListItem),
            'customerAddresses'  => $this->_getCustomerAddresses($invoiceListItem),
            'orderStatusHistory' => $this->_getOrderStatusHistory($invoiceListItem),
        ];
        
        return $rowTooltips;
    }
    
    
    /**
     * Renders and returns a template file.
     *
     * @param string $templateFile Template file to render.
     * @param array  $contentArray Content array which represent the variables of the template.
     *
     * @return string Rendered template.
     */
    protected function _render($templateFile, array $contentArray)
    {
        $this->contentView->set_content_template($templateFile);
        
        foreach ($contentArray as $contentItemKey => $contentItemValue) {
            $this->contentView->set_content_data($contentItemKey, $contentItemValue);
        }
        
        return $this->contentView->get_html();
    }
    
    
    /**
     * Get Invoice Items Tooltip HTML
     *
     * @param InvoiceListItem $invoiceListItem
     *
     * @return string
     */
    protected function _getInvoiceItems(InvoiceListItem $invoiceListItem)
    {
        $templateData = [
            'id'          => $invoiceListItem->getInvoiceId(),
            'products'    => [],
            'total_price' => '',
        ];
        
        $invoiceCurrencyCode = $this->db->get_where('invoices', ['invoice_id' => $invoiceListItem->getInvoiceId()])
            ->row()->currency;
        
        $this->db->select('orders_products.orders_products_id, 
							orders_products.products_quantity, 
							orders_products.products_name, 
							orders_products.products_model, 
							orders_products.final_price, 
							orders_products_quantity_units.unit_name')
            ->from('orders_products')
            ->join('orders_products_quantity_units',
                   'orders_products.orders_products_id = orders_products_quantity_units.orders_products_id',
                   'left outer')
            ->where('orders_id', $invoiceListItem->getOrderId());
        
        $invoiceItems = $this->db->get()->result_array();
        
        foreach ($invoiceItems as $invoiceItem) {
            $attributes = $this->db->select('products_options AS name, products_options_values AS value')
                ->from('orders_products_attributes')
                ->where('orders_products_id', $invoiceItem['orders_products_id'])
                ->get()
                ->result_array();
            
            $gPrintContentManager = new GMGPrintContentManager();
            $gPrintResult         = $gPrintContentManager->get_orders_products_content($invoiceItem['orders_products_id'],
                                                                                       true);
            
            foreach ($gPrintResult as $gPrintRow) {
                $attributes[] = [
                    'name'  => $gPrintRow['NAME'],
                    'value' => $gPrintRow['VALUE'],
                ];
            }
            
            $templateData['products'][$invoiceItem['orders_products_id']] = [
                'quantity'   => (double)$invoiceItem['products_quantity'],
                'name'       => $invoiceItem['products_name'],
                'unit_name'  => $invoiceItem['unit_name'] ? : 'x',
                'model'      => $invoiceItem['products_model'],
                'price'      => number_format((double)$invoiceItem['final_price'], 2, ',', '.') . ' '
                                . $invoiceCurrencyCode,
                'attributes' => $attributes,
            ];
        }
        
        $totalPrice = $this->db->get_where('orders_total',
                                           [
                                               'orders_id' => $invoiceListItem->getOrderId(),
                                               'class'     => 'ot_total',
                                           ])->row_array();
        
        $templateData['total_price'] = trim(strip_tags($totalPrice['title'] . ' ' . $totalPrice['text']));
        
        return $this->_render('items.html', $templateData);
    }
    
    
    /**
     * Get Customer Memo Tooltip HTML
     *
     * @param InvoiceListItem $invoiceListItem
     *
     * @return string
     */
    protected function _getCustomerMemos(InvoiceListItem $invoiceListItem)
    {
        $templateData = [
            'memos' => [],
        ];
        
        /** @var CustomerMemo $memo */
        foreach ($invoiceListItem->getCustomerMemos()->getArray() as $memo) {
            $customer = $this->db->get_where('customers', ['customers_id' => $memo->getPosterId()])->row_array();
            
            $templateData['memos'][] = [
                'title'         => '',
                'text'          => $memo->getText(),
                'creation_date' => $memo->getCreationDate(),
                'poster_name'   => $customer['customers_firstname'] . ' ' . $customer['customers_lastname'],
            ];
        }
        
        return $this->_render('customer_memos.html', $templateData);
    }
    
    
    /**
     * Get Customer Addresses Tooltip HTML
     *
     * @param InvoiceListItem $invoiceListItem
     *
     * @return string
     */
    protected function _getCustomerAddresses(InvoiceListItem $invoiceListItem)
    {
        $deliveryAddress = $invoiceListItem->getShippingAddress();
        $billingAddress  = $invoiceListItem->getPaymentAddress();
        
        $templateData = [
            'has_separate_delivery_address' => $deliveryAddress !== $billingAddress,
            'delivery'                      => [
                'firstname'               => $deliveryAddress->getFirstName(),
                'lastname'                => $deliveryAddress->getLastName(),
                'company'                 => $deliveryAddress->getCompany(),
                'street'                  => $deliveryAddress->getStreet(),
                'house_number'            => $deliveryAddress->getHouseNumber(),
                'additional_address_info' => $deliveryAddress->getAdditionalAddressInfo(),
                'postcode'                => $deliveryAddress->getPostcode(),
                'city'                    => $deliveryAddress->getCity(),
                'country'                 => $deliveryAddress->getCountry(),
            ],
            'billing'                       => [
                'firstname'               => $billingAddress->getFirstName(),
                'lastname'                => $billingAddress->getLastName(),
                'company'                 => $billingAddress->getCompany(),
                'street'                  => $billingAddress->getStreet(),
                'house_number'            => $billingAddress->getHouseNumber(),
                'additional_address_info' => $billingAddress->getAdditionalAddressInfo(),
                'postcode'                => $billingAddress->getPostcode(),
                'city'                    => $billingAddress->getCity(),
                'country'                 => $billingAddress->getCountry(),
            ],
        ];
        
        return $this->_render('customer_addresses.html', $templateData);
    }
    
    
    /**
     * Get Order Status History Tooltip HTML
     *
     * @param InvoiceListItem $invoiceListItem
     *
     * @return string
     */
    protected function _getOrderStatusHistory(InvoiceListItem $invoiceListItem)
    {
        $templateData = [
            'status_history' => [],
        ];
        
        $statusHistory = $this->db->select('orders_status_history.*, orders_status.orders_status_name AS status_name')
            ->from('orders_status_history')
            ->join('orders_status',
                   'orders_status.orders_status_id = orders_status_history.orders_status_id',
                   'left')
            ->where([
                        'orders_status_history.orders_id' => $invoiceListItem->getOrderId(),
                        'orders_status.language_id'       => $_SESSION['languages_id'],
                    ])
            ->get()
            ->result_array();
        
        foreach ($statusHistory as $entry) {
            
            $templateData['status_history'][] = [
                'status_name'          => $entry['status_name'] ? : '',
                'comment'              => $entry['comment'] ?? '',
                'date_added'           => date('d.m.Y H:i:s', strtotime($entry['date_added'])),
                'is_customer_notified' => (bool)$entry['customer_notified'],
            ];
        }
        
        return $this->_render('status_history.html', $templateData);
    }
}