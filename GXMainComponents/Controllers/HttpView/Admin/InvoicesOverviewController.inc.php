<?php

/* --------------------------------------------------------------
   InvoicesOverviewController.inc.php 2016-10-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   -------------------------------------------------------------- 
*/

MainFactory::load_class('AdminHttpViewController');

/**
 * Class InvoicesController
 *
 * @category System
 * @package  AdminHttpViewControllers
 */
class InvoicesOverviewController extends AdminHttpViewController
{
    /**
     * @var CI_DB_query_builder
     */
    protected $db;
    
    /**
     * @var UserConfigurationService
     */
    protected $userConfigurationService;
    
    /**
     * @var OrderStatusStyles
     */
    protected $orderStatusStyles;
    
    /**
     * @var InvoicesOverviewColumns
     */
    protected $invoicesOverviewColumns;
    
    
    /**
     * Initialize Controller
     */
    public function init()
    {
        $this->db                       = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $this->userConfigurationService = StaticGXCoreLoader::getService('UserConfiguration');
        $this->orderStatusStyles        = MainFactory::create('OrderStatusStyles', $this->db);
        $this->invoicesOverviewColumns  = MainFactory::create('InvoicesOverviewColumns', $this->db);
    }
    
    
    /**
     * Default Action
     *
     * Render the main order page.
     */
    public function actionDefault()
    {
        $languageTextManager = MainFactory::create('LanguageTextManager', 'admin_invoices', $_SESSION['languages_id']);
        $title               = new NonEmptyStringType($languageTextManager->get_text('PAGE_TITLE'));
        $template            = new ExistingFile(new NonEmptyStringType(DIR_FS_ADMIN
                                                                       . '/html/content/invoices/overview.html'));
        
        // Fetch the template data.
        $customerId = new IdType((int)$_SESSION['customer_id']);
        
        $pageLength = $this->userConfigurationService->getUserConfiguration($customerId, 'invoicesOverviewPageLength');
        
        $defaultColumns = [
            'invoiceNumber',
            'invoiceDate',
            'sum',
            'customer',
            'group',
            'countryIsoCode',
            'orderId',
            'orderDate',
            'paymentMethod',
            'status',
            'actions'
        ];
        
        $activeColumns = $this->userConfigurationService->getUserConfiguration($customerId,
                                                                               'invoicesOverviewSettingsColumns');
        if (empty($activeColumns)) {
            $activeColumns = [];
            /** @var DataTableColumn $dataTableColumn */
            foreach ($this->invoicesOverviewColumns->getColumns()->getArray() as $dataTableColumn) {
                $columnName = $dataTableColumn->getName();
                if (in_array($columnName, $defaultColumns, true)) {
                    $activeColumns[] = $dataTableColumn->getName();
                }
            }
            
            $activeColumns = json_encode($activeColumns);
        } else {
            $activeColumns = str_replace('\\',
                                         '',
                                         $activeColumns); // User configuration service escapes the double quotes.
        }
        
        $activeRowHeight = $this->userConfigurationService->getUserConfiguration($customerId,
                                                                                 'invoicesOverviewSettingsRowHeight');
        
        $data = MainFactory::create('KeyValueCollection',
                                    [
                                        'page_length'                  => $pageLength ? : 20,
                                        'order_status_styles'          => $this->orderStatusStyles->getStyles(),
                                        'order_status'                 => $this->_getStatuses(),
                                        'row_heights'                  => $this->_getRowHeights(),
                                        'columns'                      => $this->invoicesOverviewColumns->serializeColumns(),
                                        'default_row_action'           => $this->userConfigurationService->getUserConfiguration($customerId,
                                                                                                                                'invoicesOverviewRowAction'),
                                        'default_bulk_action'          => $this->userConfigurationService->getUserConfiguration($customerId,
                                                                                                                                'invoicesOverviewBulkAction'),
                                        'active_columns'               => $activeColumns,
                                        'active_row_height'            => $activeRowHeight ? : 'large',
                                        'max_amount_invoices_bulk_pdf' => gm_get_conf('GM_PDF_MAX_AMOUNT_INVOICES_BULK_PDF'),
                                        'default_column_settings'      => $defaultColumns,
                                        'bulk_settings_url'            => xtc_href_link('gm_pdf.php#gm_pdf_bulk'),
                                        'is_pdf_creator_installed'     => gm_pdf_is_installed()
                                    ]);
        
        $assets = MainFactory::create('AssetCollection', $this->_getAssetsArray());
        
        $contentNavigation = MainFactory::create('ContentNavigationCollection', []);
        
        $contentNavigation->add($title, new StringType('admin.php?do=InvoicesOverview'), new BoolType(true));
        
        $contentNavigation->add(new StringType($languageTextManager->get_text('SETTINGS')),
                                new StringType('gm_pdf.php'),
                                new BoolType(false));
        
        $contentNavigation->add(new StringType($languageTextManager->get_text('BOX_GM_ID_STARTS', 'admin_menu')),
                                new StringType('gm_id_starts.php'),
                                new BoolType(false));
        
        return MainFactory::create('AdminLayoutHttpControllerResponse',
                                   $title,
                                   $template,
                                   $data,
                                   $assets,
                                   $contentNavigation);
    }
    
    
    /**
     * Get Assets Array
     *
     * Overload this method in order to add your own assets to the page.
     *
     * @return array
     */
    protected function _getAssetsArray()
    {
        $assetsArray = [
            MainFactory::create('Asset', 'admin_invoices.lang.inc.php'),
            MainFactory::create('Asset', 'admin_buttons.lang.inc.php'),
            MainFactory::create('Asset', 'orders.lang.inc.php'),
            MainFactory::create('Asset', 'gm_send_order.lang.inc.php'),
            MainFactory::create('Asset', 'configuration.lang.inc.php'),
            MainFactory::create('Asset', 'lightbox_buttons.lang.inc.php')
        ];
        
        return $assetsArray;
    }
    
    
    /**
     * Returns the available row heights.
     *
     * @return array
     */
    protected function _getRowHeights()
    {
        return ['small', 'medium', 'large'];
    }
    
    
    /**
     * Order status array.
     *
     * @return array
     */
    protected function _getStatuses()
    {
        $statuses = $this->db->distinct()
            ->select('orders_status_id, orders_status_name')
            ->from('orders_status')
            ->where('language_id',
                    $_SESSION['languages_id'])
            ->get()
            ->result_array();
        
        $result = [];
        
        foreach ($statuses as $status) {
            $result[] = $status;
        }
        
        return $result;
    }
}