<?php
/* --------------------------------------------------------------
   InvoicesOverviewAjaxController.inc.php 2016-10-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class InvoicesAjaxController
 *
 * AJAX controller for the invoices main page.
 *
 * @category   System
 * @package    AdminHttpViewControllers
 * @extends    AdminHttpViewController
 */
class InvoicesOverviewAjaxController extends AdminHttpViewController
{
    /**
     * @var CI_DB_query_builder
     */
    protected $db;
    
    /**
     * @var InvoiceArchiveReadService
     */
    protected $invoiceArchiveReadService;
    /**
     * @var DataTableHelper
     */
    protected $dataTableHelper;
    
    /**
     * @var InvoicesOverviewColumns
     */
    protected $invoicesOverviewColumns;
    
    /**
     * @var InvoicesOverviewTooltips
     */
    protected $invoicesOverviewTooltips;
    
    
    /**
     * Initialize Controller
     */
    public function init()
    {
        // Check page token validity.
        $this->_validatePageToken();
        
        $this->db                        = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $this->invoiceArchiveReadService = StaticGXCoreLoader::getService('InvoiceArchiveRead');
        $this->dataTableHelper           = MainFactory::create('DataTableHelper');
        $this->invoicesOverviewColumns   = MainFactory::create('InvoicesOverviewColumns', $this->db);
        $this->invoicesOverviewTooltips  = MainFactory::create('InvoicesOverviewTooltips');
    }
    
    
    /**
     * DataTable Instance Callback
     *
     * Provides the data for the DataTables instance of the invoices overview.
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
     * This method will use the InvoicesOverviewTooltips class to render all the tooltips of the current view.
     *
     * @return JsonHttpControllerResponse
     */
    public function actionTooltips()
    {
        try {
            $invoiceListItems = $this->_getInvoiceListItems();
            
            $response = [];
            
            /** @var InvoiceListItem $invoiceListItem */
            foreach ($invoiceListItems as $invoiceListItem) {
                $response[$invoiceListItem->getInvoiceId()] = $this->invoicesOverviewTooltips->getRowTooltips($invoiceListItem);
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
     * the required values from the table row. This method will use the InvoicesOverviewColumns class to fetch the
     * latest state of the filtering options.
     *
     * @return JsonHttpControllerResponse
     */
    public function actionFilterOptions()
    {
        try {
            // Create a new instance of the InvoicesOverviewColumns in order to fetch the latest options.
            $invoicesOverviewColumns = MainFactory::create('InvoicesOverviewColumns', $this->db);
            
            $response = [];
            
            /** @var DataTableColumn $dataTableColumn */
            foreach ($invoicesOverviewColumns->getColumns() as $dataTableColumn) {
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
     * Parse the DataTable request and fetch the InvoiceListItems that need to be displayed.
     *
     * @return InvoiceListItemCollection Returns the collection with the InvoiceListItem instances.
     */
    protected function _getInvoiceListItems()
    {
        // DataTable Column Info
        $columns = $this->invoicesOverviewColumns->getColumns();
        
        // Paginate Records
        $startIndex = new IntType($_REQUEST['start']);
        $maxCount   = new IntType($_REQUEST['length']);
        
        // Sort the order records.
        $orderBy = new StringType($this->dataTableHelper->getOrderByClause($columns));
        
        // Get the filter parameters.
        $filterParameters = $this->dataTableHelper->getFilterParameters($columns);
        
        $invoiceListItems = $this->invoiceArchiveReadService->filterInvoiceList($filterParameters,
                                                                                $startIndex,
                                                                                $maxCount,
                                                                                $orderBy);
        
        return $invoiceListItems;
    }
    
    
    /**
     * Get the table data.
     *
     * This method will generate the data of the datatable instance. It can be overloaded in order to contain extra
     * data e.g. for a new column. The filtering of custom columns must be also done manually.
     *
     * @return array
     */
    protected function _getTableData()
    {
        $currenciesArray = $this->_getCurrencies();
        
        $invoiceListItems = $this->_getInvoiceListItems();
        
        $tableData = [];
        
        /** @var InvoiceListItem $invoiceListItem */
        foreach ($invoiceListItems as $invoiceListItem) {
            if (array_key_exists($invoiceListItem->getCurrency()->getCode(), $currenciesArray)) {
                $currency = $currenciesArray[$invoiceListItem->getCurrency()->getCode()];
            } else {
                $currency = $currenciesArray[DEFAULT_CURRENCY];
            }
            
            $tableData[] = [
                'DT_RowId'       => $invoiceListItem->getInvoiceId(),
                'DT_RowData'     => $this->_getRowData($invoiceListItem),
                'invoiceNumber'  => $invoiceListItem->getInvoiceNumber(),
                'invoiceDate'    => $invoiceListItem->getInvoiceDate()->format('Y-m-d H:i:s'),
                'sum'            => number_format($invoiceListItem->getTotalSum(),
                                                  (int)$currency['decimal_places'],
                                                  $currency['decimal_point'],
                                                  $currency['thousands_point']) . ' ' . $currency['symbol_right'],
                'customer'       => $invoiceListItem->getCustomerName(),
                'group'          => $invoiceListItem->getCustomerStatusName(),
                'countryIsoCode' => $invoiceListItem->getShippingAddress()->getCountryIsoCode(),
                'orderId'        => $invoiceListItem->getOrderId(),
                'orderDate'      => $invoiceListItem->getOrderDatePurchased()->format('Y-m-d H:i:s'),
                'paymentMethod'  => $invoiceListItem->getPaymentType()->getAlias(),
                'status'         => $invoiceListItem->getOrderStatusName()
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
     * @param InvoiceListItem $invoiceListItem
     *
     * @return array
     */
    protected function _getRowData(InvoiceListItem $invoiceListItem)
    {
        $rowData = [
            'statusId'              => $invoiceListItem->getOrderStatusId(),
            'orderId'               => $invoiceListItem->getOrderId(),
            'invoiceId'             => $invoiceListItem->getInvoiceId(),
            'invoiceNumber'         => $invoiceListItem->getInvoiceNumber(),
            'customerMemos'         => $invoiceListItem->getCustomerMemos()->getSerializedArray(),
            'paymentMethod'         => $invoiceListItem->getPaymentType()->getTitle(),
            'invoiceDate'           => $invoiceListItem->getInvoiceDate(),
            'filename'              => $invoiceListItem->getInvoiceFilename(),
            'country'               => $invoiceListItem->getPaymentAddress()->getCountry(),
            'customerName'          => $invoiceListItem->getCustomerName(),
            'currency'              => $invoiceListItem->getCurrency()->getCode(),
            'isCancellationInvoice' => $invoiceListItem->isCancellationInvoice()
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
        return $this->db->count_all('invoices');
    }
    
    
    /**
     * Get the filtered record count of the "orders" table.
     *
     * @return int
     */
    protected function _getFilteredRecordCount()
    {
        $columns          = $this->invoicesOverviewColumns->getColumns();
        $filterParameters = $this->dataTableHelper->getFilterParameters($columns);
        
        return $this->invoiceArchiveReadService->filterInvoiceListCount($filterParameters);
    }
    
    
    /**
     * Get the currencies with all settings like decimal point.
     *
     * @return array
     */
    protected function _getCurrencies()
    {
        $currenciesArray  = [];
        $currenciesResult = $this->db->get('currencies');
        
        foreach ($currenciesResult->result_array() as $currency) {
            $currenciesArray[$currency['code']] = $currency;
        }
        
        return $currenciesArray;
    }
}