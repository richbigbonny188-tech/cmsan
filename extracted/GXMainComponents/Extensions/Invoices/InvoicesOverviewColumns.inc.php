<?php

/* --------------------------------------------------------------
   InvoicesOverviewColumns.inc.php 2016-09-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once DIR_FS_CATALOG . 'inc/get_payment_title.inc.php';
require_once DIR_FS_CATALOG . 'inc/get_shipping_title.inc.php';

/**
 * Class InvoicesOverviewColumns
 *
 * This class contains the definition of the invoices overview columns. Use it in every controller that needs the
 * column definition.
 *
 * This class can be overloaded and more table columns can be added.
 *
 * @category   System
 * @package    Extensions
 * @subpackage Invoices
 */
class InvoicesOverviewColumns
{
    /**
     * @var CI_DB_query_builder
     */
    protected $db;
    
    /**
     * @var array
     */
    protected $columns;
    
    /**
     * @var string
     */
    protected $noValuePlaceholder;
    
    
    /**
     * OrdersOverviewColumns constructor
     *
     * Overload this method and append the $columns array with new column definitions.
     *
     * @param CI_DB_query_builder $db Will be used for database operations.
     *
     * @throws InvalidArgumentException
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
        
        $languageTextManager      = MainFactory::create('LanguageTextManager',
                                                        'admin_invoices',
                                                        $_SESSION['languages_id']);
        $this->noValuePlaceholder = '(' . $languageTextManager->get_text('no_value', 'admin_labels') . ')';
        
        // Invoice Number
        $this->columns[] = MainFactory::create('DataTableColumn')
            ->setTitle(new StringType($languageTextManager->get_text('INVOICE_NUMBER')))
            ->setName(new StringType('invoiceNumber'))
            ->setField(new StringType('invoices.invoice_number'))
            ->setType(new DataTableColumnType(DataTableColumnType::NUMBER));
        
        // Invoice Date 
        $this->columns[] = MainFactory::create('DataTableColumn')
            ->setTitle(new StringType($languageTextManager->get_text('INVOICE_DATE')))
            ->setName(new StringType('invoiceDate'))
            ->setField(new StringType('invoices.invoice_date'))
            ->setType(new DataTableColumnType(DataTableColumnType::DATE));
        
        // Total Sum
        $this->columns[] = MainFactory::create('DataTableColumn')
            ->setTitle(new StringType($languageTextManager->get_text('SUM')))
            ->setName(new StringType('sum'))
            ->setField(new StringType('invoices.total_sum'))
            ->setType(new DataTableColumnType(DataTableColumnType::NUMBER));
        
        // Customer 
        $this->columns[] = MainFactory::create('DataTableColumn')
            ->setTitle(new StringType($languageTextManager->get_text('CUSTOMER')))
            ->setName(new StringType('customer'))
            ->setField(new StringType('invoices.billing_firstname invoices.billing_lastname'))
            ->setType(new DataTableColumnType(DataTableColumnType::STRING));
        
        // Customer Group
        $this->columns[] = MainFactory::create('DataTableColumn')
            ->setTitle(new StringType($languageTextManager->get_text('GROUP')))
            ->setName(new StringType('group'))
            ->setField(new StringType('invoices.customer_status_name'))
            ->setType(new DataTableColumnType(DataTableColumnType::STRING))
            ->setOptions($this->_getGroupOptions());
        
        // Country 
        $this->columns[] = MainFactory::create('DataTableColumn')
            ->setTitle(new StringType($languageTextManager->get_text('COUNTRY')))
            ->setName(new StringType('countryIsoCode'))
            ->setField(new StringType('invoices.billing_country_iso_code_2'))
            ->setType(new DataTableColumnType(DataTableColumnType::STRING))
            ->setOptions($this->_getCountryOptions());
        
        // Order Id
        $this->columns[] = MainFactory::create('DataTableColumn')
            ->setTitle(new StringType($languageTextManager->get_text('ORDER_NUMBER')))
            ->setName(new StringType('orderId'))
            ->setField(new StringType('invoices.order_id'))
            ->setType(new DataTableColumnType(DataTableColumnType::NUMBER));
        
        // Order Date
        $this->columns[] = MainFactory::create('DataTableColumn')
            ->setTitle(new StringType($languageTextManager->get_text('ORDER_DATE')))
            ->setName(new StringType('orderDate'))
            ->setField(new StringType('invoices.order_date_purchased'))
            ->setType(new DataTableColumnType(DataTableColumnType::DATE));
        
        // Payment Method 
        $this->columns[] = MainFactory::create('DataTableColumn')
            ->setTitle(new StringType($languageTextManager->get_text('PAYMENT')))
            ->setName(new StringType('paymentMethod'))
            ->setField(new StringType('invoices.payment_class'))
            ->setType(new DataTableColumnType(DataTableColumnType::STRING))
            ->setOptions($this->_getPaymentOptions());
        
        // Order Status
        $this->columns[] = MainFactory::create('DataTableColumn')
            ->setTitle(new StringType($languageTextManager->get_text('STATUS')))
            ->setName(new StringType('status'))
            ->setField(new StringType('orders_status.orders_status_name'))
            ->setType(new DataTableColumnType(DataTableColumnType::STRING))
            ->setOptions($this->_getStatusOptions());
    }
    
    
    /**
     * Get the DataTableColumnCollection of the table.
     *
     * @return DataTableColumnCollection
     */
    public function getColumns()
    {
        return MainFactory::create('DataTableColumnCollection', $this->columns);
    }
    
    
    /**
     * Get the column definitions in a plain array.
     *
     * @return array
     */
    public function serializeColumns()
    {
        $columns = [];
        
        /** @var DataTableColumn $dataTableColumn */
        foreach ($this->columns as $dataTableColumn) {
            $columns[] = [
                'title'   => $dataTableColumn->getTitle(),
                'name'    => $dataTableColumn->getName(),
                'field'   => $dataTableColumn->getField(),
                'type'    => $dataTableColumn->getType(),
                'source'  => $dataTableColumn->getSource(),
                'options' => $dataTableColumn->getOptions()
            ];
        }
        
        return $columns;
    }
    
    
    /**
     * Get the customer group filtering options.
     *
     * @return array
     */
    public function _getGroupOptions()
    {
        $rows = $this->db->distinct()
            ->select('orders.customers_status AS id, orders.customers_status_name AS fallback_name, '
                     . 'customers_status.customers_status_name AS status_name')
            ->from('invoices')
            ->join('orders', 'orders.orders_id = invoices.order_id', 'inner')
            ->join('customers_status',
                   'customers_status.customers_status_id = orders.customers_status',
                   'left')
            ->join('languages', 'languages.languages_id = customers_status.language_id', 'left')
            ->where('languages.languages_id',
                    $_SESSION['languages_id'])
            ->get()
            ->result_array();
        
        $options = [];
        
        foreach ($rows as $row) {
            $option = [
                // Value 
                $row['id'],
                // Text
                $row['status_name'] !== '' ? $row['status_name'] : $row['fallback_name']
            ];
            
            if (!in_array($option, $options)) {
                $options[] = $option;
            }
        }
        
        return $this->_getMultiSelectOptions($options);
    }
    
    
    /**
     * Get the order status filtering options.
     *
     * @return array
     */
    public function _getStatusOptions()
    {
        $rows = $this->db->distinct()
            ->select('orders.orders_status AS status_id, orders_status.orders_status_name AS status_name')
            ->from('invoices')
            ->join('orders', 'orders.orders_id = invoices.order_id', 'inner')
            ->join('orders_status',
                   'orders_status.orders_status_id = orders.orders_status',
                   'inner')
            ->where('orders_status.language_id', $_SESSION['languages_id'])
            ->get()
            ->result_array();
        
        $options = [];
        
        foreach ($rows as $row) {
            $options[] = [
                // Value 
                $row['status_id'],
                // Text
                $row['status_name']
            ];
        }
        
        return $this->_getMultiSelectOptions($options);
    }
    
    
    /**
     * Get the payment method filtering options.
     *
     * @return array
     */
    public function _getPaymentOptions()
    {
        $rows = $this->db->distinct()->select('payment_class')->get('invoices')->result_array();
        
        $options = [];
        
        foreach ($rows as $row) {
            $aliasConstant      = 'MODULE_PAYMENT_' . strtoupper($row['payment_class']) . '_ALIAS';
            $paymentModuleTitle = $row['payment_class'] ? get_payment_title($row['payment_class']) : '';
            
            $options[] = [
                // Value
                $row['payment_class'],
                // Text 
                defined($aliasConstant) ? constant($aliasConstant) . ': ' . $paymentModuleTitle : $paymentModuleTitle
            ];
        }
        
        return $this->_getMultiSelectOptions($options);
    }
    
    
    /**
     * Get the country filtering options.
     *
     * @return array
     */
    protected function _getCountryOptions()
    {
        $options = $this->db->distinct()
            ->select('billing_country_iso_code_2, billing_country')
            ->get('invoices')
            ->result_array();
        
        return $this->_getMultiSelectOptions($options);
    }
    
    
    /**
     * Convert the option to [value, text] arrays.
     *
     * The result of this method can be given back to multi_select widgets. If an option has different value
     * and text then the first entry must always be the value and the second the text to be displayed.
     *
     * @param array $options Contains the options of the multi_select widget.
     *
     * @return array
     */
    protected function _getMultiSelectOptions(array $options)
    {
        $multiSelectOptions = [];
        
        foreach ($options as $option) {
            if (is_array($option)) {
                $multiSelectOption = [
                    'value' => array_shift($option),
                    'text'  => array_shift($option)
                ];
            } else {
                $multiSelectOption = [
                    'value' => $option,
                    'text'  => $option
                ];
            }
            
            if ($multiSelectOption['text'] === '') {
                $multiSelectOption['text'] = $this->noValuePlaceholder;
            }
            
            $multiSelectOptions[] = $multiSelectOption;
        }
        
        return $multiSelectOptions;
    }
}