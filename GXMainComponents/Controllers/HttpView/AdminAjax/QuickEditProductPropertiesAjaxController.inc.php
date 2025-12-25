<?php

/* --------------------------------------------------------------
   QuickEditProductPropertiesAjaxController.inc.php 2017-03-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class QuickEditProductPropertiesAjaxController
 *
 * Serves properties modal operations of the QuickEdit page.
 *
 * @category System
 * @package  AdminHttpViewControllers
 */
class QuickEditProductPropertiesAjaxController extends AdminHttpViewController
{
    /**
     * @var CI_DB_query_builder
     */
    protected $db;
    
    /**
     * @var int
     */
    protected $propertiesCount;
    
    /**
     * @var int
     */
    protected $propertiesTotal;
    
    /**
     * @var QuickEditService
     */
    protected $quickEditService;
    
    /**
     * @var QuickEditPropertiesOverviewColumns
     */
    protected $quickEditPropertiesOverviewColumns;
    
    /**
     * @var QuickEditPropertiesTooltips
     */
    protected $quickEditPropertiesOverviewTooltips;
    
    /**
     * @var DataTableHelper $dataTableHelper
     */
    protected $dataTableHelper;
    
    
    /**
     * Initializes the required objects.
     */
    public function init()
    {
        // Check page token validity.
        $this->_validatePageToken();
        
        $this->db                                  = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $this->quickEditPropertiesOverviewColumns  = MainFactory::create('QuickEditPropertiesOverviewColumns');
        $this->quickEditPropertiesOverviewTooltips = MainFactory::create('QuickEditPropertiesTooltips');
        $this->dataTableHelper                     = MainFactory::create('DataTableHelper');
        
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
                'data'            => $this->_getProductsProperties(),
                'draw'            => (int)$_REQUEST['draw'],
                'recordsFiltered' => $this->propertiesCount,
                'recordsTotal'    => $this->propertiesTotal
            ];
        } catch (Exception $ex) {
            $response = AjaxException::response($ex);
        }
        
        return MainFactory::create('JsonHttpControllerResponse', $response);
    }
    
    
    /**
     * Returns the tooltips for the overview page in JSON format.
     *
     * @return bool|JsonHttpControllerResponse Returns the tooltips for the overview page in JSON format.
     */
    public function actionTooltips()
    {
        try {
            $response = [];
            $columns  = $this->quickEditPropertiesOverviewColumns->getColumns();
            $products = $_REQUEST['productId'];
            $start    = new IntType($_REQUEST['start']);
            $length   = new IntType($_REQUEST['length']);
            $orderBy  = new StringType($this->dataTableHelper->getOrderByClause($columns));
            $filter   = $this->dataTableHelper->getFilterParameters($columns);
            
            $properties = $this->quickEditService->paginateProperties($start, $length)
                ->sortProperties($orderBy)
                ->getFilteredProductProperties($products, $filter);
            
            foreach ($properties as $property) {
                /** @var QuickEditProductPropertiesListItem $property */
                $response[$property->getId()] = $this->quickEditPropertiesOverviewTooltips->getRowTooltips($property);
            }
        } catch (Exception $ex) {
            $response = AjaxException::response($ex);
        }
        
        return MainFactory::create('JsonHttpControllerResponse', $response);
    }
    
    
    /**
     * Updates property information.
     *
     * @return bool|JsonHttpControllerResponse Status message in JSON format.
     */
    public function actionUpdate()
    {
        try {
            $result   = $this->quickEditService->setByCombisId($_REQUEST['data']);
            $response = ['success' => $result];
        } catch (Exception $ex) {
            $response = AjaxException::response($ex);
        }
        
        return MainFactory::create('JsonHttpControllerResponse', $response);
    }
    
    
    /**
     * Returns the product properties for the existing products.
     *
     * @return array Returns the product properties for the existing products.
     */
    protected function _getProductsProperties()
    {
        $columns  = $this->quickEditPropertiesOverviewColumns->getColumns();
        $products = $_REQUEST['productId'];
        $start    = new IntType($_REQUEST['start']);
        $length   = new IntType($_REQUEST['length']);
        $orderBy  = new StringType($this->dataTableHelper->getOrderByClause($columns));
        $filter   = $this->dataTableHelper->getFilterParameters($columns);
        
        $properties = $this->quickEditService->paginateProperties($start, $length)
            ->sortProperties($orderBy)
            ->getFilteredProductProperties($products, $filter);
        
        $this->propertiesCount = $this->quickEditService->getFilteredProductPropertiesCount($products, $filter);
        $this->propertiesTotal = $this->quickEditService->getProductPropertiesCount();
        
        return array_map(function ($property) {
            /** @var QuickEditProductPropertiesListItem $property */
            
            return [
                'DT_RowId'            => $property->getId(),
                'combiId'             => $property->getId(),
                'productsName'        => $property->getProductsName(),
                'combiName'           => $property->getName(),
                'combiModel'          => $property->getModel(),
                'combiEan'            => $property->getEan(),
                'combiQuantity'       => $property->getQuantity(),
                'combiShippingTimeId' => $property->getShippingTimeId(),
                'combiWeight'         => $property->getWeight(),
                'combiPrice'          => $property->getPrice(),
                'combiPriceType'      => $property->getPriceType(),
                'option'              => [
                    'shipment'  => $property->getShipmentConfiguration(),
                    'priceType' => $property->getPriceTypeConfiguration()
                ]
            ];
        },
            $properties);
    }
}