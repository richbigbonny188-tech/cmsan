<?php
/* --------------------------------------------------------------
   OrdersOverviewColumns.inc.php 2021-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once DIR_FS_CATALOG . 'inc/get_payment_title.inc.php';
require_once DIR_FS_CATALOG . 'inc/get_shipping_title.inc.php';

/**
 * Class OrdersOverviewColumns
 *
 * This class contains the definition of the order columns. Use it in every controller that needs the
 * column definition.
 *
 * This class can be overloaded and more table columns can be added.
 *
 * @category   System
 * @package    Extensions
 * @subpackage Orders
 */
class OrdersOverviewColumns
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
     * @throws InvalidArgumentException
     */
    public function __construct()
    {
        $this->db = StaticGXCoreLoader::getDatabaseQueryBuilder();
        
        $languageTextManager      = MainFactory::create('LanguageTextManager',
                                                        'admin_orders',
                                                        $_SESSION['languages_id']);
        $this->noValuePlaceholder = '(' . $languageTextManager->get_text('no_value', 'admin_labels') . ')';
        
        // Number
        $this->columns[] = MainFactory::create('DataTableColumn')
            ->setTitle(new StringType($languageTextManager->get_text('NUMBER')))
            ->setName(new StringType('number'))
            ->setField(new StringType('orders.orders_id'))
            ->setType(new DataTableColumnType(DataTableColumnType::NUMBER));
        
        // Customer
        $this->columns[] = MainFactory::create('DataTableColumn')
            ->setTitle(new StringType($languageTextManager->get_text('CUSTOMER')))
            ->setName(new StringType('customer'))
            ->setField(new StringType('orders.customers_lastname orders.customers_firstname'))
            ->setType(new DataTableColumnType(DataTableColumnType::STRING));
        
        // Group
        $this->columns[] = MainFactory::create('DataTableColumn')
            ->setTitle(new StringType($languageTextManager->get_text('GROUP')))
            ->setName(new StringType('group'))
            ->setField(new StringType('orders.customers_status_name'))
            ->setType(new DataTableColumnType(DataTableColumnType::STRING))
            ->setOptions($this->_getGroupOptions());
        
        // Sum
        $this->columns[] = MainFactory::create('DataTableColumn')
            ->setTitle(new StringType($languageTextManager->get_text('SUM')))
            ->setName(new StringType('sum'))
            ->setField(new StringType('orders_total.value'))
            ->setType(new DataTableColumnType(DataTableColumnType::NUMBER));
        
        // Payment Method
        $this->columns[] = MainFactory::create('DataTableColumn')
            ->setTitle(new StringType($languageTextManager->get_text('PAYMENT')))
            ->setName(new StringType('paymentMethod'))
            ->setField(new StringType('orders.payment_class'))
            ->setType(new DataTableColumnType(DataTableColumnType::STRING))
            ->setOptions($this->_getPaymentOptions());
        
        // Shipping Method
        $this->columns[] = MainFactory::create('DataTableColumn')
            ->setTitle(new StringType($languageTextManager->get_text('SHIPPING')))
            ->setName(new StringType('shippingMethod'))
            ->setField(new StringType('orders.shipping_class'))
            ->setType(new DataTableColumnType(DataTableColumnType::STRING))
            ->setOptions($this->_getShippingOptions());
        
        // Country ISO Code
        $this->columns[] = MainFactory::create('DataTableColumn')
            ->setTitle(new StringType($languageTextManager->get_text('COUNTRY')))
            ->setName(new StringType('countryIsoCode'))
            ->setField(new StringType('orders.delivery_country_iso_code_2'))
            ->setType(new DataTableColumnType(DataTableColumnType::STRING))
            ->setOptions($this->_getCountryOptions());
        
        // Date
        $this->columns[] = MainFactory::create('DataTableColumn')
            ->setTitle(new StringType($languageTextManager->get_text('DATE')))
            ->setName(new StringType('date'))
            ->setField(new StringType('orders.date_purchased'))
            ->setType(new DataTableColumnType(DataTableColumnType::DATE));
        
        // Status
        $this->columns[] = MainFactory::create('DataTableColumn')
            ->setTitle(new StringType($languageTextManager->get_text('STATUS')))
            ->setName(new StringType('status'))
            ->setField(new StringType('orders_status.orders_status_name'))
            ->setType(new DataTableColumnType(DataTableColumnType::STRING))
            ->setOptions($this->_getStatusOptions());
        
        // Total Weight
        $this->columns[] = MainFactory::create('DataTableColumn')
            ->setTitle(new StringType($languageTextManager->get_text('ORDER_WEIGHT_UNIT')))
            ->setName(new StringType('totalWeight'))
            ->setField(new StringType('orders.order_total_weight'))
            ->setType(new DataTableColumnType(DataTableColumnType::NUMBER));
        
        // Invoice numbers
        $this->columns[] = MainFactory::create('DataTableColumn')
            ->setTitle(new StringType($languageTextManager->get_text('INVOICE_NUMBER', 'admin_invoices')))
            ->setName(new StringType('invoiceNumber'))
            ->setField(new StringType('invoice_numbers'))
            ->setType(new DataTableColumnType(DataTableColumnType::STRING));
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
            ->from('orders')
            ->join('customers_status',
                   'customers_status.customers_status_id = orders.customers_status',
                   'left')
            ->join('languages', 'languages.languages_id = customers_status.language_id', 'left')
            ->where('languages.languages_id',
                    $_SESSION['languages_id'])
            ->get()
            ->result_array();
        
        $options   = [];
        $statusIds = [];
        
        foreach ($rows as $row) {
            $option = [
                // Value
                $row['id'],
                // Text
                $row['status_name'] !== '' ? $row['status_name'] : $row['fallback_name']
            ];
            
            if (!in_array($option, $options)) {
                $statusIds[] = $row['id'];
                $options[]   = $option;
            }
        }
        
        // get ids and names of deleted customer groups
        $rows = $this->db->distinct()
            ->select('orders.customers_status AS id, orders.customers_status_name AS fallback_name')
            ->from('orders')
            ->get()
            ->result_array();
        
        foreach ($rows as $row) {
            if (in_array($row['id'], $statusIds, true)) {
                continue;
            }
            
            $option = [
                // Value
                $row['id'],
                // Text
                $row['fallback_name']
            ];
            
            $statusIds[] = $row['id'];
            $options[]   = $option;
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
            ->from('orders')
            ->join('orders_status', 'orders_status.orders_status_id = orders.orders_status', 'inner')
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
        $rows = $this->db->distinct()->select('payment_class')->get('orders')->result_array();
        
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
     * Get the shipping method filtering options.
     *
     * @return array
     */
    public function _getShippingOptions()
    {
        $rows = $this->db->distinct()->select('shipping_class')->get('orders')->result_array();
        
        $options = [];
        
        foreach ($rows as $row) {
            // Handle special case where a module name is doubled (e.g. flat_flat).
            $explodedClassName = explode('_', $row['shipping_class']);
            $shippingClassName = (count($explodedClassName) === 2
                                  && $explodedClassName[0]
                                     === $explodedClassName[1]) ? $explodedClassName[0] : $row['shipping_class'];
            
            $aliasConstant       = 'MODULE_SHIPPING_' . strtoupper($shippingClassName) . '_ALIAS';
            $shippingModuleTitle = ($shippingClassName === '' ? '' : get_shipping_title($shippingClassName));
            
            $options[] = [
                // Value
                $row['shipping_class'],
                // Text
                defined($aliasConstant) ? constant($aliasConstant) . ': ' . $shippingModuleTitle : $shippingModuleTitle
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
        $languageTextManager      = MainFactory::create('LanguageTextManager',
                                                        'countries',
                                                        $_SESSION['languages_id']);
        
        $options = $this->db->distinct()
            ->select('delivery_country_iso_code_2, delivery_country')
            ->get('orders')
            ->result_array();
        foreach ($options as $option) {
            $countries[] = [
                $option['delivery_country_iso_code_2'],
                $languageTextManager->get_text($option['delivery_country_iso_code_2'])
            ];
        }
        return $this->_getMultiSelectOptions($countries ?? []);
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
            
            if ($multiSelectOption['value'] === '') {
                $multiSelectOption['value'] = OrderListGenerator::FILTER_NO_VALUE;
            }
            
            $multiSelectOptions[] = $multiSelectOption;
        }
        
        return $multiSelectOptions;
    }
}