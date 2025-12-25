<?php
/* --------------------------------------------------------------
   OrdersOverviewAjaxController.inc.php 2021-08-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class OrdersAjaxController
 *
 * AJAX controller for the orders main page.
 *
 * @category   System
 * @package    AdminHttpViewControllers
 * @extends    AdminHttpViewController
 */
class OrdersOverviewAjaxController extends AdminHttpViewController
{
    /**
     * @var CI_DB_query_builder
     */
    protected $db;
    
    /**
     * @var OrderReadService
     */
    protected $orderReadService;
    
    /**
     * @var DataTableHelper
     */
    protected $dataTableHelper;
    
    /**
     * @var OrdersOverviewColumns
     */
    protected $ordersOverviewColumns;
    
    /**
     * @var OrdersOverviewTooltips
     */
    protected $ordersOverviewTooltips;
    
    
    /**
     * Initialize Controller
     *
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function init()
    {
        // Check page token validity.
        $this->_validatePageToken();
        
        // Instantiate required classes.
        $this->db                     = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $this->orderReadService       = StaticGXCoreLoader::getService('OrderRead');
        $this->dataTableHelper        = MainFactory::create('DataTableHelper');
        $this->ordersOverviewColumns  = MainFactory::create('OrdersOverviewColumns');
        $this->ordersOverviewTooltips = MainFactory::create('OrdersOverviewTooltips');
    }
    
    
    /**
     * DataTable Instance Callback
     *
     * Provides the data for the DataTables instance of the orders main view.
     */
    public function actionDataTable()
    {
        try {
            $tableData = $this->_getTableData();
            
            $response = [
                'draw'            => (int)$_REQUEST['draw'],
                'recordsTotal'    => $this->_getTotalRecordCount(),
                'recordsFiltered' => $this->_getFilteredRecordCount(),
                'data'            => $tableData
            ];
        } catch (Exception $ex) {
            $response = AjaxException::response($ex);
        }
        
        return MainFactory::create('JsonHttpControllerResponse', $response);
    }
    
    
    /**
     * DataTable Tooltips Rendering
     *
     * This method will use the OrdersOverviewTooltips class to render all the tooltips of the current view.
     *
     * @return JsonHttpControllerResponse
     */
    public function actionTooltips()
    {
        try {
            $orderListItems = $this->_getOrderListItems();
            
            $response = [];
            
            /** @var OrderListItem $orderListItem */
            foreach ($orderListItems as $orderListItem) {
                $response[$orderListItem->getOrderId()] = $this->ordersOverviewTooltips->getRowTooltips($orderListItem);
            }
        } catch (Exception $ex) {
            $response = AjaxException::response($ex);
        }
        
        return MainFactory::create('JsonHttpControllerResponse', $response);
    }
    
    
    /**
     * Regenerate the filtering options and send them back to the client.
     *
     * After some specific changes the table filtering options will need to be updated because they do not contain
     * the required values from the table row. This method will use the OrdersOverviewColumns class to fetch the
     * latest state of the filtering options.
     *
     * @return JsonHttpControllerResponse
     */
    public function actionFilterOptions()
    {
        try {
            // Create a new instance of the OrdersOverviewColumns in order to fetch the latest options.
            $ordersOverviewColumns = MainFactory::create('OrdersOverviewColumns');
            
            $response = [];
            
            /** @var DataTableColumn $dataTableColumn */
            foreach ($ordersOverviewColumns->getColumns() as $dataTableColumn) {
                $options = $dataTableColumn->getOptions();
                
                if (count($options) > 0) {
                    $response[$dataTableColumn->getName()] = $dataTableColumn->getOptions();
                }
            }
        } catch (Exception $ex) {
            $response = AjaxException::response($ex);
        }
        
        return MainFactory::create('JsonHttpControllerResponse', $response);
    }
    
    
    /**
     * Returns an array of invoices data for an order.
     *
     * @return JsonHttpControllerResponse Invoice data list.
     */
    public function actionGetInvoices()
    {
        /**
         * @var $invoiceArchiveReadService InvoiceArchiveReadService
         * @var $invoices                  InvoiceListItemCollection
         * @var $invoice                   InvoiceListItem
         */
        
        // Order ID.
        $orderId = (int)$_REQUEST['id'];
        
        // Invoice Archive Read Service.
        $invoiceArchiveReadService = StaticGXCoreLoader::getService('InvoiceArchiveRead');
        
        // Get invoices by order ID.
        $invoices = $invoiceArchiveReadService->getInvoiceListByConditions(['order_id' => $orderId],
                                                                           null,
                                                                           null,
                                                                           new StringType('invoice_date DESC'));
        
        // Collection of invoices.
        $result = [];
        
        // Iterate over each invoice and push their data to the array.
        if (!$invoices->isEmpty()) {
            foreach ($invoices->getArray() as $invoice) {
                $result[] = [
                    'invoiceId'     => $invoice->getInvoiceId(),
                    'invoiceNumber' => $invoice->getInvoiceNumber(),
                    'orderId'       => $invoice->getOrderId(),
                    'filename'      => $invoice->getInvoiceFilename()
                ];
            }
        }
        
        return MainFactory::create('JsonHttpControllerResponse', $result);
    }
    
    
    /**
     * Get the latest packing slip information.
     *
     * @return JsonHttpControllerResponse Returns the information of the latest packing slip.
     */
    public function actionGetLatestPackingSlip()
    {
        $result = [];
        
        $orderID     = $_REQUEST['id'];
        $packingSlip = $this->db->select(['packing_slip_id', 'filename'])
            ->where('order_id', $orderID)
            ->order_by('date',
                       'DESC')
            ->limit(1)
            ->get('packing_slips')
            ->row();
        
        if (!empty($packingSlip)) {
            $result[] = [
                'id'   => $packingSlip->packing_slip_id,
                'file' => $packingSlip->filename
            ];
        }
        
        return MainFactory::create('JsonHttpControllerResponse', $result);
    }
    
    
    public function actionGetOrdersWithoutDocuments()
    {
        try {
            $type     = $this->_getQueryParameter('type');
            $orderIds = $this->_getQueryParameter('orderIds');
            
            if ($type !== 'invoice' && $type !== 'packingslip') {
                throw new InvalidArgumentException('Invalid Type Parameter: ' . $type);
            }
            
            foreach ($orderIds as $orderId) {
                $this->db->or_where('order_id', $orderId);
            }
            
            $table  = $type === 'invoice' ? 'invoices' : 'packing_slips';
            $result = $this->db->distinct()->select('order_id')->get($table)->result_array();
            
            $result = array_map(function ($value) {
                return $value['order_id'];
            },
                $result);
            
            $response = array_values(array_diff($orderIds, $result));
        } catch (Exception $ex) {
            $response = AjaxException::response($ex);
        }
        
        return MainFactory::create('JsonHttpControllerResponse', $response);
    }
    
    
    /**
     * Parse the DataTable request and fetch the OrderListItems that need to be displayed.
     *
     * @return OrderListItemCollection Returns the collection with the OrderListItem instances.
     *
     * @throws InvalidArgumentException
     * @throws Exception
     */
    protected function _getOrderListItems()
    {
        // DataTable Column Info
        $columns = $this->ordersOverviewColumns->getColumns();
        
        // Paginate Records
        $startIndex = new IntType($_REQUEST['start']);
        $maxCount   = new IntType($_REQUEST['length']);
        $page       = (int)floor($startIndex->asInt() / $maxCount->asInt() + 1);
        $sorters    = $this->getSorters($columns);
        
        // Get the filter parameters.
        $filterParameters = $this->dataTableHelper->getFilterParameters($columns);
        
        $orderListItems = $this->orderReadService->filterOrderList($filterParameters,
                                                                   Pager::create($page, $maxCount->asInt()),
                                                                   $sorters);
        
        return $orderListItems;
    }
    
    
    /**
     * Get the table data.
     *
     * This method will generate the data of the datatable instance. It can be overloaded in order to contain extra
     * data e.g. for a new column. The filtering of custom columns must be also done manually.
     *
     * @return array
     *
     * @throws InvalidArgumentException
     * @throws Exception
     */
    protected function _getTableData()
    {
        $orderListItems = $this->_getOrderListItems();
        
        /* @var InvoiceArchiveReadService $invoiceReadService */
        $invoiceReadService = StaticGXCoreLoader::getService('InvoiceArchiveRead');
        
        $tableData = [];
        
        /** @var OrderListItem $orderListItem */
        foreach ($orderListItems as $orderListItem) {
            $customerName    = trim($orderListItem->getCustomerName())
                               === '' ? $orderListItem->getCustomerCompany() : $orderListItem->getCustomerName();
            $orderId         = $orderListItem->getOrderId();
            $latestInvoice   = $invoiceReadService->getInvoiceListByConditions(['order_id' => $orderId],
                                                                               new IntType(0),
                                                                               new IntType(1),
                                                                               new StringType('invoice_date DESC'))
                ->getArray();
            $latestInvoiceId = array_key_exists(0, $latestInvoice) ? $latestInvoice[0]->getInvoiceId() : 0;
            
            $latestInvoiceNumber = null;
            if ($latestInvoiceId !== 0) {
                $latestInvoiceNumber = $latestInvoice[0]->getInvoiceNumber();
            }
            
            $tableData[] = [
                'countryIsoCode' => $orderListItem->getDeliveryAddress()->getCountryIsoCode(),
                'customer'       => $customerName,
                'date'           => $orderListItem->getPurchaseDateTime()->format('Y-m-d H:i:s'),
                'DT_RowId'       => $orderId,
                'DT_RowData'     => $this->_getRowData($orderListItem, $latestInvoiceId, $latestInvoiceNumber),
                'group'          => $orderListItem->getCustomerStatusName(),
                'paymentMethod'  => $orderListItem->getPaymentType()->getAlias(),
                'shippingMethod' => $orderListItem->getShippingType()->getAlias(),
                'status'         => $orderListItem->getOrderStatusName(),
                'sum'            => $orderListItem->getTotalSum(),
                'totalWeight'    => $orderListItem->getTotalWeight(),
            ];
        }
        
        return $tableData;
    }
    
    
    /**
     * Set the <tr> row data.
     *
     * This method will return an array which will contain the data attributes of each row. These data are
     * used in JS as follows: "$('tr').data('propertyName')".
     *
     * Overload this method to provide your own data to the rows.
     *
     * @param OrderListItem $orderListItem
     * @param int           $latestInvoiceId
     * @param int           $latestInvoiceNumber
     *
     * @return array
     */
    protected function _getRowData(OrderListItem $orderListItem, $latestInvoiceId = 0, $latestInvoiceNumber = null)
    {
        $rowData = [
            'comment'             => $orderListItem->getComment(),
            'customerEmail'       => $orderListItem->getCustomerEmail(),
            'customerId'          => $orderListItem->getCustomerId(),
            'customerMemos'       => $orderListItem->getCustomerMemos()->getSerializedArray(),
            'customerName'        => $orderListItem->getCustomerName(),
            'id'                  => $orderListItem->getOrderId(),
            'mailStatus'          => $orderListItem->getMailStatus(),
            'purchaseDate'        => $orderListItem->getPurchaseDateTime(),
            'statusId'            => $orderListItem->getOrderStatusId(),
            'trackingLinks'       => $orderListItem->getTrackingLinks()->getStringArray(),
            'withdrawalIds'       => $orderListItem->getWithdrawalIds()->getIntArray(),
            'paymentMethod'       => $orderListItem->getPaymentType()->getTitle(),
            'shippingMethod'      => $orderListItem->getShippingType()->getTitle(),
            'country'             => $orderListItem->getDeliveryAddress()->getCountry(),
            'invoiceNumbers'      => $orderListItem->getInvoiceNumbers()->getStringArray(),
            'latestInvoiceId'     => $latestInvoiceId,
            'latestInvoiceNumber' => $latestInvoiceNumber
        
        ];
        
        return $rowData;
    }
    
    
    /**
     * Get the total record count of the "orders" table.
     *
     * @return int
     */
    protected function _getTotalRecordCount()
    {
        static $totalRecordCount;
    
        if ($totalRecordCount === null) {
            $db = StaticGXCoreLoader::getDatabaseQueryBuilder();
        
            $totalRecordCount = $db->count_all('orders');
        }
    
        return $totalRecordCount;
    }
    
    
    /**
     * Get the filtered record count of the "orders" table.
     *
     * @return int
     *
     * @throws BadMethodCallException
     * @throws InvalidArgumentException
     */
    protected function _getFilteredRecordCount()
    {
        $columns          = $this->ordersOverviewColumns->getColumns();
        $filterParameters = $this->dataTableHelper->getFilterParameters($columns);
        
        return !empty($filterParameters) ? $this->orderReadService->filterOrderListCount($filterParameters) : $this->_getTotalRecordCount();
    }
    
    
    /**
     * @param \DataTableColumnCollection $columns
     *
     * @return array
     * @throws \Exception
     */
    protected function getSorters(DataTableColumnCollection $columns)
    {
        // Sort the order records.
        $orderBy = new StringType($this->dataTableHelper->getOrderByClause($columns));
        
        $orderByData = preg_split('/,? /', $orderBy->asString());
        
        $sorters = [];
        
        for ($i = 0, $iMax = count($orderByData); $i < $iMax; $i += 2) {
            if (strtoupper($orderByData[$i + 1]) === 'ASC') {
                $sorters[] = new Sorter(new StringType('+' . $orderByData[$i]));
            } else {
                $sorters[] = new Sorter(new StringType('-' . $orderByData[$i]));
            }
        }
        
        return $sorters;
    }
}
