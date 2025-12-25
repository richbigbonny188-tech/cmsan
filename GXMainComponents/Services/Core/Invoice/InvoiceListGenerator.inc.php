<?php
/* --------------------------------------------------------------
   InvoiceListGenerator.inc.php 2023-11-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class InvoiceListGenerator
 *
 * @category   System
 * @package    Invoice
 */
class InvoiceListGenerator implements InvoiceListGeneratorInterface
{
    /**
     * @var CI_DB_query_builder
     */
    protected $db;
    
    
    /**
     * @var int
     */
    protected $defaultLanguageId;
    
    /**
     * @var PaymentTitleProvider
     */
    protected $paymentTitleProvider;
    
    
    /**
     * InvoiceListGenerator constructor.
     *
     * @param CI_DB_query_builder  $db
     * @param PaymentTitleProvider $paymentTitleProvider
     */
    public function __construct(CI_DB_query_builder $db, PaymentTitleProvider $paymentTitleProvider)
    {
        $this->db = $db;
        
        $this->defaultLanguageId = $this->db->select('languages_id')
            ->from('languages')
            ->where('code', 'de')
            ->get()
            ->row()->languages_id;
        
        $this->paymentTitleProvider = $paymentTitleProvider;
    }
    
    
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
        array      $conditions = [],
        IntType    $startIndex = null,
        IntType    $maxCount = null,
        StringType $orderBy = null
    ) {
        $this->_select()->_limit($startIndex, $maxCount)->_order($orderBy);
        
        $this->db->group_start();
        $this->db->where('orders_status.language_id', $this->defaultLanguageId);
        $this->db->or_where('orders_status.language_id IS NULL');
        $this->db->group_end();
        
        if (count($conditions) > 0) {
            $this->db->where($conditions);
        }
        
        $result = $this->db->get()->result_array();
        
        return $this->_prepareCollection($result);
    }
    
    
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
        array      $filterParameters,
        IntType    $startIndex = null,
        IntType    $maxCount = null,
        StringType $orderBy = null
    ) {
        $result = $this->_filter($filterParameters, $startIndex, $maxCount, $orderBy);
        
        return $this->_prepareCollection($result->result_array());
    }
    
    
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
    public function filterInvoiceListCount(array $filterParameters)
    {
        $result = $this->_filter($filterParameters);
        
        return $result->num_rows();
    }
    
    
    /**
     * Prepares the InvoiceListItemCollection by the passed result array.
     *
     * @param array $resultArray Result array with fetched invoice data.
     *
     * @return InvoiceListItemCollection
     */
    protected function _prepareCollection(array $resultArray)
    {
        $items = [];
        foreach ($resultArray as $row) {
            $invoiceId     = new IdType($row['invoice_id']);
            $invoiceNumber = new StringType($row['invoice_number']);
            
            $row['invoice_date'] = $row['invoice_date'] === null ? '1000-01-01 00:00:00' : $row['invoice_date'];
            
            $invoiceDate     = new DateTime($row['invoice_date']);
            $invoiceFilename = new FilenameStringType($row['invoice_file']);
            $totalSum        = new DecimalType((double)$row['total_sum']);
            
            $row['currency'] = $row['currency'] === null ? DEFAULT_CURRENCY : $row['currency'];
            
            $currency           = MainFactory::create('CurrencyCode', new StringType((string)$row['currency']));
            $customerId         = new IdType($row['customer_id']);
            $customerName       = new StringType($row['customers_firstname'] . ' ' . $row['customers_lastname']);
            $customerStatusId   = new IdType((int)$row['customer_status_id']);
            $customerStatusName = new StringType($row['customer_status_name']);
            $customerMemos      = $this->_createMemoCollectionByCustomersId($row['customer_id']);
            
            $paymentAddress  = $this->_createOrderAddressBlockByRow('billing', $row);
            $shippingAddress = $this->_createOrderAddressBlockByRow('delivery', $row);
            
            $orderId               = new IdType($row['order_id']);
            $row['date_purchased'] = $row['order_date_purchased']
                                     === null ? $row['date_purchased'] : $row['order_date_purchased'];
            $orderDatePurchased    = new DateTime($row['date_purchased']);
            $paymentType           = $this->_createOrderType($row['payment_class'], $row['payment_method']);
            
            $orderStatusId   = new IdType((int)$row['orders_status']);
            $orderStatusName = new StringType((string)$row['orders_status_name']);
            
            $invoiceListItem = MainFactory::create('InvoiceListItem');
            
            $invoiceListItem->setInvoiceId($invoiceId)
                ->setInvoiceNumber($invoiceNumber)
                ->setInvoiceFilename($invoiceFilename)
                ->setInvoiceDate($invoiceDate)
                ->setTotalSum($totalSum)
                ->setCurrency($currency)
                ->setCustomerId($customerId)
                ->setCustomerName($customerName)
                ->setCustomerStatusId($customerStatusId)
                ->setCustomerStatusName($customerStatusName)
                ->setCustomerMemos($customerMemos)
                ->setPaymentAddress($paymentAddress)
                ->setShippingAddress($shippingAddress)
                ->setOrderId($orderId)
                ->setOrderDatePurchased($orderDatePurchased)
                ->setPaymentType($paymentType)
                ->setOrderStatusId($orderStatusId)
                ->setOrderStatusName($orderStatusName);
            
            $items[] = $invoiceListItem;
        }
        
        return MainFactory::create('InvoiceListItemCollection', $items);
    }
    
    
    /**
     * Creates an order address block object by the given type and row_array (looped result of CIDB::result_array())
     *
     * @param string $type Whether delivery or billing.
     * @param array  $row  Array which contain data about an order result row.
     *
     * @Todo Equal to OrderListGenerator::_createOrderAddressBlockByRow() method. Maybe outsource in abstract parent.
     *
     * @return OrderAddressBlock
     */
    protected function _createOrderAddressBlockByRow($type, array $row)
    {
        if ($type !== 'delivery' && $type !== 'billing') {
            throw new BadMethodCallException('the "$type" argument has to be equal to whether "delivery" or "billing"');
        }
        
        $firstName             = new StringType($row[$type . '_firstname']);
        $lastName              = new StringType($row[$type . '_lastname']);
        $company               = new StringType((string)$row[$type . '_company']);
        $streetAddress         = new StringType($row[$type . '_street_address']);
        $houseNumber           = new StringType($row[$type . '_house_number']);
        $additionalAddressInfo = new StringType($row[$type . '_additional_info']);
        $postCode              = new StringType($row[$type . '_postcode']);
        $city                  = new StringType($row[$type . '_city']);
        $state                 = new StringType((string)$row[$type . '_state']);
        $country               = new StringType($row[$type . '_country']);
        $countryIsoCode        = new StringType($row[$type . '_country_iso_code_2']);
        $gender                = isset($row[$type . '_gender']) ? new StringType($row[$type . '_gender']) : null;
        
        return MainFactory::create('OrderAddressBlock',
                                   $firstName,
                                   $lastName,
                                   $company,
                                   $streetAddress,
                                   $houseNumber,
                                   $additionalAddressInfo,
                                   $postCode,
                                   $city,
                                   $state,
                                   $country,
                                   $countryIsoCode,
                                   $gender);
    }
    
    
    /**
     * Creates and returns a customer memo collection by the given customers id.
     *
     * @param int $customersId Id of customer.
     *
     * @Todo Equal to OrderListGenerator::_createMemoCollectionByCustomersId() method. Maybe outsource in abstract
     *       parent.
     *
     * @return CustomerMemoCollection
     */
    protected function _createMemoCollectionByCustomersId($customersId)
    {
        $memoArray = $this->db->get_where('customers_memo', ['customers_id' => $customersId])->result_array();
        $memos     = [];
        
        foreach ($memoArray as $customerMemo) {
            $memos[] = MainFactory::create('CustomerMemo',
                                           new IdType($customerMemo['customers_id']),
                                           new StringType($customerMemo['memo_text']),
                                           new DateTime($customerMemo['memo_date']),
                                           new IdType($customerMemo['poster_id']));
        }
        
        return MainFactory::create('CustomerMemoCollection', $memos);
    }
    
    
    /**
     * Execute the select and join methods.
     *
     * @return $this|InvoiceListGenerator Returns the instance object for method chaining.
     */
    protected function _select()
    {
        $columns = [
            $this->_invoicesColumns(),
            $this->_ordersColumns(),
            $this->_ordersStatusColumns(),
        ];
        
        $this->db->select(implode(',', $columns))
            ->from('invoices')
            ->join('orders',
                   'orders.orders_id = invoices.order_id',
                   'left outer')
            ->join('orders_status', 'orders.orders_status = orders_status.orders_status_id', 'left outer')
            ->join('customers',
                   'customers.customers_id = invoices.customer_id',
                   'left outer');
        
        return $this;
    }
    
    
    /**
     * Returns a string for the ::_select() method which contains column names of the invoices table.
     *
     * @return string
     */
    protected function _invoicesColumns()
    {
        return 'invoices.*';
    }
    
    
    /**
     * Returns a string for the ::_select() method which contains column names of the orders table.
     *
     * @return string
     */
    protected function _ordersColumns()
    {
        return 'orders.orders_status, orders.date_purchased';
    }
    
    
    /**
     * Returns a string for the ::_select() method which contains column names of the orders_status table.
     *
     * @return string
     */
    protected function _ordersStatusColumns()
    {
        return 'orders_status.orders_status_name';
    }
    
    
    /**
     * Add limit configuration to the database object.
     *
     * @param IntType $startIndex
     * @param IntType $maxCount
     *
     * @Todo Equal to OrderListGenerator::_limit() method. Maybe outsource in abstract parent.
     *
     * @return $this|InvoiceListGenerator Returns the instance object for method chaining.
     */
    protected function _limit(IntType $startIndex = null, IntType $maxCount = null)
    {
        if ($maxCount && $startIndex) {
            $this->db->limit($maxCount->asInt(), $startIndex->asInt());
        } else {
            if ($maxCount && !$startIndex) {
                $this->db->limit($maxCount->asInt());
            }
        }
        
        return $this;
    }
    
    
    /**
     * Set the order by clause of the query.
     *
     * @param StringType $orderBy
     *
     * @Todo Equal to OrderListGenerator::_order() method. Maybe outsource in abstract parent.
     *
     * @return $this|InvoiceListGenerator Returns the instance object for method chaining.
     */
    protected function _order(StringType $orderBy = null)
    {
        if ($orderBy) {
            $this->db->order_by($orderBy->asString());
        }
        
        return $this;
    }
    
    
    /**
     * Execute the group by statement.
     *
     * @return InvoiceListGenerator Returns the instance object for method chaining.
     */
    protected function _group()
    {
        $this->db->group_by('invoices.invoice_id, orders_status.orders_status_name');
        
        return $this;
    }
    
    
    /**
     * Filter the invoice records.
     *
     * This method contains the filtering logic. It can be overloaded in order to provide a custom filtering logic.
     *
     * @param array           $filterParameters Contains the column slug-names and their values.
     * @param IntType|null    $startIndex       The start index of the wanted array to be returned (default = null).
     * @param IntType|null    $maxCount         Maximum amount of items which should be returned (default = null).
     * @param StringType|null $orderBy          A string which defines how the items should be ordered (default = null).
     *
     * @return CI_DB_result
     *
     * @throws BadMethodCallException
     */
    protected function _filter(
        array      $filterParameters,
        IntType    $startIndex = null,
        IntType    $maxCount = null,
        StringType $orderBy = null
    ) {
        $this->_select()->_limit($startIndex, $maxCount)->_order($orderBy)->_group();
        
        $this->db->group_start();
        $this->db->where('orders_status.language_id', $this->defaultLanguageId);
        $this->db->or_where('orders_status.language_id IS NULL');
        $this->db->group_end();
        
        // Replace wildcards recursively
        array_walk($filterParameters,
            function (&$value, $key) {
                if (!is_array($value) && $value !== ''
                    && !in_array($key,
                                 [
                                     'invoiceDate',
                                     'orderDate',
                                     'group',
                                     'countryIsoCode',
                                     'paymentMethod',
                                     'status',
                                 ])) {
                    $value = str_replace('*', '%', $this->db->escape_str($value));
                }
            });
        
        // Filter by invoice number.
        if (!empty($filterParameters['invoiceNumber'])) {
            $this->db->where('`invoices`.`invoice_number` LIKE "' . $filterParameters['invoiceNumber'] . '"');
        }
        
        // Filter by invoice date.
        $dateFormat = array_key_exists('language_code', $_SESSION)
                      && $_SESSION['language_code'] === 'de' ? 'd.m.y' : 'm.d.y';
        if (isset($filterParameters['invoiceDate']) && is_array($filterParameters['invoiceDate'])) {
            $dateValue = DateTime::createFromFormat($dateFormat, array_shift($filterParameters['invoiceDate']));
            $this->db->where('invoices.invoice_date >=', $dateValue->format('Y-m-d'));
            $dateValue = DateTime::createFromFormat($dateFormat, array_shift($filterParameters['invoiceDate']));
            $this->db->where('invoices.invoice_date <=', $dateValue->format('Y-m-d') . ' 23:59:59');
        } else {
            if (isset($filterParameters['invoiceDate']) && !empty($filterParameters['invoiceDate'])) {
                $dateValue = DateTime::createFromFormat($dateFormat, $filterParameters['invoiceDate']);
                $this->db->where('invoices.invoice_date >=', $dateValue->format('Y-m-d') . ' 00:00:00');
                $this->db->where('invoices.invoice_date <=', $dateValue->format('Y-m-d') . ' 23:59:59');
            }
        }
        
        // Filter by total sum.
        if (isset($filterParameters['sum']) && is_array($filterParameters['sum'])) {
            $this->db->where('invoices.total_sum >=', $filterParameters['sum'][0]);
            $this->db->where('invoices.total_sum <=', $filterParameters['sum'][1]);
        } else {
            if (!empty($filterParameters['sum'])) {
                $filterParameters['sum'] = str_replace(',', '.', $filterParameters['sum']);
                if (strpos($filterParameters['sum'], '%') !== false) {
                    $this->db->where('`invoices`.`total_sum` LIKE "' . $filterParameters['sum'] . '"');
                } else {
                    $this->db->where('invoices.total_sum', $filterParameters['sum']);
                }
            }
        }
        
        // Filter by customer.
        if (!empty($filterParameters['customer'])) {
            $this->db->group_start();
            $this->db->where('`invoices`.`billing_firstname` LIKE "' . $filterParameters['customer'] . '"')
                ->or_where('`invoices`.`billing_lastname` LIKE "' . $filterParameters['customer'] . '"')
                ->or_where('`customers`.`customers_firstname` LIKE "' . $filterParameters['customer'] . '"')
                ->or_where('`customers`.`customers_lastname` LIKE "' . $filterParameters['customer'] . '"')
                ->or_where('`customers`.`customers_id` LIKE "' . $filterParameters['customer'] . '"')
                ->or_where('`customers`.`customers_cid` LIKE "' . $filterParameters['customer'] . '"')
                ->or_where('`customers`.`customers_vat_id` LIKE "' . $filterParameters['customer'] . '"')
                ->or_where('`customers`.`customers_gender` LIKE "' . $filterParameters['customer'] . '"')
                ->or_where('`customers`.`customers_email_address` LIKE "' . $filterParameters['customer'] . '"')
                ->or_where('`customers`.`customers_telephone` LIKE "' . $filterParameters['customer'] . '"')
                ->or_where('`customers`.`customers_fax` LIKE "' . $filterParameters['customer'] . '"');
            $this->db->group_end();
        }
        
        // Filter by customer group.
        if (isset($filterParameters['group']) && is_array($filterParameters['group'])) {
            $groups = $filterParameters['group'];
            $this->db->group_start()->where('invoices.customer_status_id', array_shift($groups));
            foreach ($groups as $group) {
                $this->db->or_where('invoices.customer_status_id', $group);
            }
            $this->db->group_end();
        }
        
        // Filter by country ISO code.
        if (isset($filterParameters['countryIsoCode']) && is_array($filterParameters['countryIsoCode'])) {
            $countryIsoCodes = $filterParameters['countryIsoCode'];
            $this->db->group_start()->where('invoices.billing_country_iso_code_2', array_shift($countryIsoCodes));
            foreach ($countryIsoCodes as $countryIsoCode) {
                $this->db->or_where('invoices.billing_country_iso_code_2', $countryIsoCode);
            }
            $this->db->group_end();
        }
        
        // Filter by order number.
        if (isset($filterParameters['orderId']) && is_array($filterParameters['orderId'])) {
            $this->db->where('invoices.order_id >=', array_shift($filterParameters['orderId']));
            $this->db->where('invoices.order_id <=', array_shift($filterParameters['orderId']));
        } else {
            if (isset($filterParameters['orderId']) && !empty($filterParameters['orderId'])) {
                $this->db->where('`invoices`.`order_id` LIKE "' . $filterParameters['orderId'] . '"');
            }
        }
        
        // Filter by order date.
        $dateFormat = array_key_exists('language_code', $_SESSION)
                      && $_SESSION['language_code'] === 'de' ? 'd.m.y' : 'm.d.y';
        if (isset($filterParameters['orderDate']) && is_array($filterParameters['orderDate'])) {
            $dateValue = DateTime::createFromFormat($dateFormat, array_shift($filterParameters['orderDate']));
            $this->db->where('invoices.order_date_purchased >=', $dateValue->format('Y-m-d'));
            $dateValue = DateTime::createFromFormat($dateFormat, array_shift($filterParameters['orderDate']));
            $this->db->where('invoices.order_date_purchased <=', $dateValue->format('Y-m-d') . ' 23:59:59');
        } else {
            if (isset($filterParameters['orderDate']) && !empty($filterParameters['orderDate'])) {
                $dateValue = DateTime::createFromFormat($dateFormat, $filterParameters['orderDate']);
                $this->db->where('invoices.order_date_purchased >=', $dateValue->format('Y-m-d'));
                $this->db->where('invoices.order_date_purchased <=', $dateValue->format('Y-m-d') . ' 23:59:59');
            }
        }
        
        // Filter by payment.
        if (isset($filterParameters['paymentMethod']) && is_array($filterParameters['paymentMethod'])) {
            $paymentMethods = $filterParameters['paymentMethod'];
            $this->db->group_start()->where('invoices.payment_class', array_shift($paymentMethods));
            foreach ($paymentMethods as $payment) {
                $this->db->or_where('invoices.payment_class', $payment);
            }
            $this->db->group_end();
        }
        
        // Filter by order status.
        if (isset($filterParameters['status']) && is_array($filterParameters['status'])) {
            $this->db->group_start()->where('orders.orders_status', array_shift($filterParameters['status']));
            foreach ($filterParameters['status'] as $status) {
                $this->db->or_where('orders.orders_status', $status);
            }
            $this->db->group_end();
        }
        
        return $this->db->get();
    }
    
    
    /**
     * Creates and returns payment type instance by the given class and method argument.
     *
     * @param $class  data about the class type.
     * @param $method data about the method type.
     *
     * @return OrderPaymentType
     *
     * @throws InvalidArgumentException
     */
    protected function _createOrderType($class, $method)
    {
        $explodedMethodName = explode('_', $method);
        
        $method = (count($explodedMethodName) === 2
                   && $explodedMethodName[0] === $explodedMethodName[1]) ? $explodedMethodName[0] : $method;
        $title  = $method ? $this->paymentTitleProvider->title($method) : '';
        
        $explodedClassName = explode('_', $class);
        
        $class = (count($explodedClassName) === 2
                  && $explodedClassName[0] === $explodedClassName[1]) ? $explodedClassName[0] : $class;
        
        $configurationValue = $this->db->get_where('gx_configurations',
                                                   [
                                                       'key' => 'configuration/MODULE_PAYMENT_' . strtoupper($class)
                                                                . '_ALIAS',
                                                   ]);
        
        $alias = $configurationValue->num_rows() ? new StringType($configurationValue->row()->value) : null;
        
        return MainFactory::create('OrderPaymentType', new StringType($title), new StringType($method), $alias);
    }
}
