<?php
/* --------------------------------------------------------------
   QuickEditSpecialPriceController.inc.php 2018-04-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class QuickEditSpecialPricesAjaxController
 *
 * @category System
 * @package  AdminHttpViewControllers
 */
class QuickEditSpecialPricesAjaxController extends AdminHttpViewController
{
    /**
     * @var CI_DB_query_builder
     */
    protected $db;
    
    /**
     * @var int
     */
    protected $specialPriceCount;
    
    /**
     * @var int
     */
    protected $specialRecordsTotal;
    
    /**
     * @var QuickEditService
     */
    protected $quickEditService;
    
    /**
     * @var QuickEditSpecialPricesOverviewColumns
     */
    protected $quickEditSpecialPricesOverviewColumns;
    
    /**
     * @var QuickEditSpecialPriceTooltips
     */
    protected $quickEditSpecialPricesOverviewTooltips;
    
    /**
     * @var DataTableHelper
     */
    protected $dataTableHelper;
    
    
    /**
     * Initializes the required objects.
     */
    public function init()
    {
        $this->_validatePageToken();
        
        $this->db                                     = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $this->quickEditSpecialPricesOverviewColumns  = MainFactory::create('QuickEditSpecialPricesOverviewColumns');
        $this->quickEditSpecialPricesOverviewTooltips = MainFactory::create('QuickEditSpecialPriceTooltips');
        $this->dataTableHelper                        = MainFactory::create('DataTableHelper');
        
        $quickEditServiceFactory = MainFactory::create('QuickEditServiceFactory', $this->db);
        $this->quickEditService  = $quickEditServiceFactory->createQuickEditService();
    }
    
    
    /**
     * Returns all required data to display the table contents.
     *
     * @return bool|JsonHttpControllerResponse Returns all required data to display the table contents.
     */
    public function actionDataTable()
    {
        try {
            $response = [
                'data'            => $this->_getProductSpecialPrices(),
                'draw'            => (int)$_REQUEST['draw'],
                'recordsFiltered' => $this->specialPriceCount,
                'recordsTotal'    => $this->specialRecordsTotal
            ];
        } catch (Exception $ex) {
            $response = AjaxException::response($ex);
        }
        
        return MainFactory::create('JsonHttpControllerResponse', $response);
    }
    
    
    /**
     * Returns the tooltips for the special prices table in JSON format.
     *
     * @return bool|JsonHttpControllerResponse Returns the tooltips for the special prices table in JSON format.
     * @todo Implement tooltip functionality for the special prices table.
     *
     */
    public function actionTooltips()
    {
        try {
            $response = [];
            
            //$columns  = $this->quickEditSpecialPricesOverviewColumns->getColumns();
            //$products = $_REQUEST['productId'];
            //$start    = new IntType($_REQUEST['start']);
            //$length   = new IntType($_REQUEST['length']);
            //$orderBy  = new StringType($this->dataTableHelper->getOrderByClause($columns));
            //$filter   = $this->dataTableHelper->getFilterParameters($columns);
            
            //$productsSpecialPrice = $this->quickEditService->paginateSpecialPrices($start, $length)
            //                                                ->sortSpecialPrices($orderBy)
            //                                                ->getFilteredSpecialPrices($products, $filter);
            
            //foreach($productsSpecialPrice as $special)
            //{
            //	/** @var QuickEditProductSpecialPriceListItem $special */
            //	$response[$special->getProductsId()] = $this->quickEditSpecialPricesOverviewTooltips->getRowTooltips($special);
            //}
        } catch (Exception $ex) {
            $response = AjaxException::response($ex);
        }
        
        return MainFactory::create('JsonHttpControllerResponse', $response);
    }
    
    
    /**
     * Updates special price information.
     *
     * @return bool|JsonHttpControllerResponse Returns status message in JSON format.
     */
    public function actionUpdate()
    {
        try {
            $result   = $this->quickEditService->setSpecialPriceById($_REQUEST['data']);
            $response = ['success' => $result];
        } catch (Exception $ex) {
            $response = AjaxException::response($ex);
        }
        
        return MainFactory::create('JsonHttpControllerResponse', $response);
    }
    
    
    /**
     * Deletes a special price.
     *
     * @return bool|JsonHttpControllerResponse Status message in JSON format.
     */
    public function actionDelete()
    {
        try {
            $this->db->where_in('products_id', $_REQUEST['products']);
            $result = $this->db->delete('specials');
            
            $response = ['success' => $result];
        } catch (Exception $ex) {
            $response = AjaxException::response($ex);
        }
        
        return MainFactory::create('JsonHttpControllerResponse', $response);
    }
    
    
    /**
     * Returns the special prices for the existing products.
     *
     * @return array Returns the special prices for the existing products.
     */
    protected function _getProductSpecialPrices()
    {
        $columns  = $this->quickEditSpecialPricesOverviewColumns->getColumns();
        $products = $_REQUEST['productId'];
        $start    = new IntType($_REQUEST['start']);
        $length   = new IntType($_REQUEST['length']);
        $orderBy  = new StringType($this->dataTableHelper->getOrderByClause($columns));
        $filter   = $this->dataTableHelper->getFilterParameters($columns);
        
        $productsSpecialsPrice = $this->quickEditService->paginateSpecialPrices($start, $length)
            ->sortSpecialPrices($orderBy)
            ->getFilteredSpecialPrices($products, $filter);
        
        $this->specialPriceCount   = $this->quickEditService->getFilteredSpecialPricesCount($products, $filter);
        $this->specialRecordsTotal = $this->quickEditService->getSpecialPricesCount();
        
        return array_map(function ($specialPrice) {
            /** @var QuickEditProductSpecialPriceListItem $specialPrice */
            $expiresDate = $specialPrice->getExpiresDate();
            
            return [
                'DT_RowId'                => $specialPrice->getProductsId(),
                'productsName'            => $specialPrice->getProductsName(),
                'productsModel'           => $specialPrice->getProductsModel(),
                'productsPrice'           => $specialPrice->getProductsPrice(),
                'specialPriceQuantity'    => $specialPrice->getQuantity(),
                'specialPrice'            => $specialPrice->getPrice(),
                'specialPriceIsNewEntry'  => $specialPrice->isNewEntry(),
                'specialPriceExpiresDate' => $expiresDate !== '01.01.1000'
                                             && $expiresDate !== '01.01.1970' ? $expiresDate : '',
                'specialPriceStatus'      => $specialPrice->getStatus(),
            ];
        },
            $productsSpecialsPrice);
    }
}