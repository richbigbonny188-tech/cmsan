<?php

/* --------------------------------------------------------------
   QuickEditOverviewController.inc.php 2017-03-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class QuickEditOverviewController
 *
 * Bootstraps the QuickEdit overview page.
 *
 * @extends  AdminHttpViewController
 * @category System
 * @package  AdminHttpViewControllers
 */
class QuickEditOverviewController extends AdminHttpViewController
{
    /**
     * @var CI_DB_query_builder
     */
    protected $db;
    
    /**
     * @var QuickEditOverviewColumns
     */
    protected $quickEditOverviewColumns = [];
    
    /**
     * @var QuickEditPropertiesOverviewColumns
     */
    protected $quickEditPropertiesOverviewColumns = [];
    
    /**
     * @var QuickEditSpecialPricesOverviewColumns
     */
    protected $quickEditSpecialPricesOverviewColumns = [];
    
    /**
     * @var UserConfigurationService
     */
    protected $userConfigurationService;
    
    
    /**
     * Initializes the required objects.
     */
    public function init()
    {
        $this->db                                    = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $this->userConfigurationService              = StaticGXCoreLoader::getService('UserConfiguration');
        $this->quickEditOverviewColumns              = MainFactory::create('QuickEditOverviewColumns');
        $this->quickEditPropertiesOverviewColumns    = MainFactory::create('QuickEditPropertiesOverviewColumns');
        $this->quickEditSpecialPricesOverviewColumns = MainFactory::create('QuickEditSpecialPricesOverviewColumns');
    }
    
    
    /**
     * Renders quickEdit overview page.
     *
     * @return \AdminLayoutHttpControllerResponse|bool Returns the layout response of quickEdit overview page.
     */
    public function actionDefault()
    {
        $languageTextManager = MainFactory::create('LanguageTextManager',
                                                   'admin_quick_edit',
                                                   $_SESSION['languages_id']);
        $title               = new NonEmptyStringType($languageTextManager->get_text('PAGE_TITLE'));
        $template            = new ExistingFile(new NonEmptyStringType(DIR_FS_ADMIN
                                                                       . '/html/content/quick_edit/overview.html'));
        
        $customerId = new IdType((int)$_SESSION['customer_id']);
        $pageLength = $this->userConfigurationService->getUserConfiguration($customerId, 'quickEditOverviewPageLength');
        
        $defaultColumns = [
            'category',
            'name',
            'model',
            'quantity',
            'price',
            'discount',
            'specialPrice',
            'tax',
            'shippingStatusName',
            'weight',
            'shippingCosts',
            'status'
        ];
        
        $activeColumns = $this->userConfigurationService->getUserConfiguration($customerId,
                                                                               'quickEditOverviewSettingsColumns');
        
        if (empty($activeColumns)) {
            $activeColumns = [];
            /** @var DataTableColumn $dataTableColumn */
            foreach ($this->quickEditOverviewColumns->getColumns()->getArray() as $dataTableColumn) {
                $columnName = $dataTableColumn->getName();
                if (in_array($columnName, $defaultColumns, true)) {
                    $activeColumns[] = $dataTableColumn->getName();
                }
            }
            
            $activeColumns = json_encode($activeColumns);
        } else {
            $activeColumns = str_replace('\\', '', $activeColumns);
        }
        
        $activeRowHeight = $this->userConfigurationService->getUserConfiguration($customerId,
                                                                                 'quickEditOverviewSettingsRowHeight');
        
        $data = MainFactory::create('KeyValueCollection',
                                    [
                                        'page_length'                  => $pageLength ? : 25,
                                        'columns'                      => $this->quickEditOverviewColumns->serializeColumns(),
                                        'active_columns'               => $activeColumns,
                                        'properties_columns'           => $this->quickEditPropertiesOverviewColumns->serializeColumns(),
                                        'properties_active_columns'    => $this->_getPropertiesColumns(),
                                        'special_price_columns'        => $this->quickEditSpecialPricesOverviewColumns->serializeColumns(),
                                        'special_price_active_columns' => $this->_getSpecialPriceColumns(),
                                        'row_heights'                  => $this->_getRowHeights(),
                                        'active_row_height'            => $activeRowHeight ? : 'large',
                                        'default_row_action'           => $this->userConfigurationService->getUserConfiguration($customerId,
                                                                                                                                'quickEditOverviewRowAction'),
                                        'default_bulk_action'          => $this->userConfigurationService->getUserConfiguration($customerId,
                                                                                                                                'quickEditOverviewBulkAction'),
                                        'pdf_creator_installed'        => gm_pdf_is_installed()
                                    ]);
        
        $assets = MainFactory::create('AssetCollection', $this->_getAssetsArray());
        
        return MainFactory::create('AdminLayoutHttpControllerResponse', $title, $template, $data, $assets);
    }
    
    
    /**
     * Returns the assets as an array.
     *
     * @return array Returns the assets as an array.
     */
    protected function _getAssetsArray()
    {
        $assetsArray = [
            MainFactory::create('Asset', 'admin_quick_edit.lang.inc.php')
        ];
        
        return $assetsArray;
    }
    
    
    /**
     * Returns the row heights as an array.
     *
     * @return array Returns the row heights as an array.
     */
    protected function _getRowHeights()
    {
        return ['small', 'medium', 'large'];
    }
    
    
    /**
     * Returns the required columns for the properties in JSON format.
     *
     * @return string Returns the required columns for the properties in JSON format.
     */
    protected function _getPropertiesColumns()
    {
        $columns = [
            'productsName',
            'combiName',
            'combiModel',
            'combiEan',
            'combiQuantity',
            'combiPrice',
            'combiPriceType',
            'combiShippingStatusName',
            'combiWeight',
        ];
        
        return json_encode($columns);
    }
    
    
    /**
     * Returns the required columns for the special prices in JSON format.
     *
     * @return string Returns the required columns for the special prices in JSON format.
     */
    protected function _getSpecialPriceColumns()
    {
        $columns = [
            'productsName',
            'productsModel',
            'productsPrice',
            'specialPrice',
            'specialPriceQuantity',
            'specialPriceExpiresDate',
            'specialPriceStatus',
        ];
        
        return json_encode($columns);
    }
}