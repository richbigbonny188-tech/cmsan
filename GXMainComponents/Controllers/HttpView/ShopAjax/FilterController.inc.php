<?php
/* --------------------------------------------------------------
   FilterController.inc.php 2019-09-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class FilterController
 *
 * @extends    HttpViewController
 * @category   System
 * @package    HttpViewControllers
 */
class FilterController extends HttpViewController
{
    /**
     * @return JsonHttpControllerResponse
     * @todo use GET and POST REST-API like
     *
     */
    public function actionDefault()
    {
        $filterContentView = MainFactory::create_object('FilterBoxThemeContentView');
        $filterContentView->setFilterFormContent();
        $filterContentView->setCategoryId($this->_getPostData('feature_categories_id'));
        $filterContentView->setLanguageId($_SESSION['languages_id']);
        $filterContentView->setSelectedValuesArray($this->_getPostData('filter_fv_id'));
        $filterContentView->setPriceStart($this->_getPostData('filter_price_min'));
        $filterContentView->setPriceEnd($this->_getPostData('filter_price_max'));
        $filterContentView->setFilterUrl($this->_getPostData('filter_url'));
        $result = $filterContentView->get_html($this->_getPostData('feature_categories_id'),
                                               $_SESSION['languages_id'],
                                               $this->_getPostData('filter_fv_id'),
                                               $this->_getPostData('filter_price_min'),
                                               $this->_getPostData('filter_price_max'),
                                               $this->_getPostData('filter_url'));
        $result = [
            'success' => true,
            'content' => [
                'filter' => [
                    'selector' => 'filterForm',
                    'type'     => 'replace',
                    'value'    => $result
                ]
            ]
        ];
        
        return MainFactory::create('JsonHttpControllerResponse', $result);
    }
    
    
    /**
     * @return JsonHttpControllerResponse
     * @todo use GET and POST REST-API like
     *
     */
    public function actionGetListing()
    {
    
        $applicationBottomExtenderComponent = MainFactory::create_object('ApplicationBottomExtenderComponent');
        $applicationBottomExtenderComponent->set_data('GET', $_GET);
        $applicationBottomExtenderComponent->init_page();
    
        $coo_mn_menu_boxes_control = MainFactory::create_object('MenuBoxesContentControl',
                                                                [
                                                                    $GLOBALS['coo_template_control'],
                                                                    $applicationBottomExtenderComponent->get_page()
                                                                ]);
    
        $coo_mn_menu_boxes_control->set_('account_type', $_SESSION['account_type'] == '0' ? '0' : null);
        $coo_mn_menu_boxes_control->set_('c_path', $GLOBALS['cPath']);
        $coo_mn_menu_boxes_control->set_('category_id', $GLOBALS['cID'] ?? 0);
        $coo_mn_menu_boxes_control->set_('customer_id', $_SESSION['customer_id'] ?? null);
        $coo_mn_menu_boxes_control->set_('request_type', $GLOBALS['request_type']);
        $coo_mn_menu_boxes_control->set_('coo_product', $GLOBALS['product']);
        $coo_mn_menu_boxes_control->set_('coo_xtc_price', $GLOBALS['xtPrice']);
        $coo_mn_menu_boxes_control->proceed();
    
        /** @var ProductListingContentControl $listingContentControl */
        $listingContentControl = MainFactory::create_object('ProductListingContentControl');
        $listingContentControl->set_('coo_mn_data_container', $coo_mn_menu_boxes_control);
        
        $listingContentControl->set_data('GET', $this->_getQueryParametersCollection()->getArray());
        
        $listingContentControl->set_('c_path', $GLOBALS['cPath']);
        
        if (!is_null($this->_getQueryParameter('cat'))) {
            $listingContentControl->set_('cat', $this->_getQueryParameter('cat'));
        }
        
        if (isset($GLOBALS['cID'])) {
            $listingContentControl->set_('categories_id', $GLOBALS['cID']);
        }
        
        $listingContentControl->set_('coo_filter_manager', $_SESSION['coo_filter_manager']);
        $listingContentControl->set_('coo_product', $GLOBALS['product']);
        $listingContentControl->set_('currency_code', $_SESSION['currency']);
        $listingContentControl->set_('current_category_id', $GLOBALS['current_category_id']);
        $listingContentControl->set_('current_page', basename($GLOBALS['PHP_SELF']));
        
        if (isset($_SESSION['customer_country_id'])) {
            $listingContentControl->set_('customer_country_id', $_SESSION['customer_country_id']);
        } else {
            $listingContentControl->set_('customer_country_id', STORE_COUNTRY);
        }
        
        if (isset($_SESSION['customer_zone_id'])) {
            $listingContentControl->set_('customer_zone_id', $_SESSION['customer_zone_id']);
        } else {
            $listingContentControl->set_('customer_zone_id', STORE_ZONE);
        }
        
        $listingContentControl->set_('customers_fsk18_display',
                                     $_SESSION['customers_status']['customers_fsk18_display']);
        $listingContentControl->set_('customers_status_id', $_SESSION['customers_status']['customers_status_id']);
        
        if (!is_null($this->_getQueryParameter('filter_fv_id'))) {
            $listingContentControl->set_('filter_fv_id', $this->_getQueryParameter('filter_fv_id'));
        }
        
        if (!is_null($this->_getQueryParameter('filter_id'))) {
            $listingContentControl->set_('filter_id', $this->_getQueryParameter('filter_id'));
        }
        
        if (!is_null($this->_getQueryParameter('filter_price_min'))) {
            $listingContentControl->set_('filter_price_min', $this->_getQueryParameter('filter_price_min'));
        }
        
        if (!is_null($this->_getQueryParameter('filter_price_max'))) {
            $listingContentControl->set_('filter_price_max', $this->_getQueryParameter('filter_price_max'));
        }
        
        if (!is_null($this->_getQueryParameter('feature_categories_id'))) {
            $listingContentControl->set_('feature_categories_id', $this->_getQueryParameter('feature_categories_id'));
        }
        
        if (empty($_SESSION['customers_status']['customers_status_graduated_prices'])) {
            $listingContentControl->set_('show_graduated_prices', false);
        } else {
            $listingContentControl->set_('show_graduated_prices', true);
        }
        
        $listingContentControl->set_('languages_id', $_SESSION['languages_id']);
        
        if (isset($_SESSION['last_listing_sql']) == false) {
            $_SESSION['last_listing_sql'] = '';
        }
        $listingContentControl->reference_set_('last_listing_sql', $_SESSION['last_listing_sql']);
        
        if (!is_null($this->_getQueryParameter('value_conjunction'))) {
            $listingContentControl->set_('value_conjunction', $this->_getQueryParameter('value_conjunction'));
        }
        
        $listingContentControl->set_('show_price_tax',
                                     $_SESSION['customers_status']['customers_status_show_price_tax']);
        
        $listingContentControl->init_feature_filter();
        $filterManager = $listingContentControl->get_filter_manager();
        
        if (isset($_GET['reset'])) {
            $filterManager->reset();
        }
        
        if ($listingContentControl->determine_category_depth() === 'top'
            && strpos($_GET['filter_url'], 'advanced_search_result.php') === false) {
            $result = [
                'success'  => true,
                'redirect' => xtc_href_link(FILENAME_DEFAULT)
            ];
            
            return MainFactory::create('JsonHttpControllerResponse', $result);
        } elseif (!$filterManager->is_active()) {
            $result = [
                'success'  => true,
                'redirect' => GM_HTTP_SERVER . DIR_WS_CATALOG . $_GET['filter_url']
            ];
            
            return MainFactory::create('JsonHttpControllerResponse', $result);
        }
        
        $filterSelection = $listingContentControl->get_filter_selection_html_output();
        
        $listingContentControl->activateFilterListing();
        $listingContentControl->setProductListingMainTemplate();
        $listingContentControl->proceed();
        $products = $listingContentControl->get_response();
        
        if ($listingContentControl->isEmptyResult()) {
            unset($_GET['do']);
            $parameters = http_build_query($_GET, '', '&', PHP_QUERY_RFC3986);
            
            $result = [
                'success'  => true,
                'redirect' => GM_HTTP_SERVER . DIR_WS_CATALOG . $_GET['filter_url'] . '?' . $parameters
            ];
            
            return MainFactory::create('JsonHttpControllerResponse', $result);
        }
        
        //$listingContentControl->setProductListingTemplatePath('pagination_info.html');
        $listingContentControl->setPaginationInfoTemplate();
        $listingContentControl->proceed();
        $paginationInfo = $listingContentControl->get_response();
        
        //$listingContentControl->setProductListingTemplatePath('product_listing_hidden_fields.html');
        $listingContentControl->setProductListingHiddenFieldsTemplate();
        $listingContentControl->proceed();
        $hiddenFields = $listingContentControl->get_response();
        
        //$listingContentControl->setProductListingTemplatePath('pagination.html');
        $listingContentControl->setProductListingPaginationTemplate();
        $listingContentControl->proceed();
        $pagination = $listingContentControl->get_response();
        
        $result = [
            'success' => true,
            'content' => [
                'products'       => [
                    'selector' => 'productsContainer',
                    'type'     => 'html',
                    'value'    => $products
                ],
                'filter'         => [
                    'selector' => 'filterSelectionContainer',
                    'type'     => 'replace',
                    'value'    => $filterSelection
                ],
                'pagination'     => [
                    'selector' => 'listingPagination',
                    'type'     => 'replace',
                    'value'    => $pagination
                ],
                'hiddens'        => [
                    'selector' => 'filterHiddenContainer',
                    'type'     => 'replace',
                    'value'    => $hiddenFields
                ],
                'paginationInfo' => [
                    'selector' => 'paginationInfo',
                    'type'     => 'replace',
                    'value'    => $paginationInfo
                ]
            ]
        ];
        
        return MainFactory::create('JsonHttpControllerResponse', $result);
    }
    
    
    /**
     * @param mixed $result
     *
     * @return array
     */
    protected function _convertResult($result)
    {
        $result = [
            'success' => true,
            'content' => [
                'filter' => [
                    'selector' => 'filterForm',
                    'type'     => 'replace',
                    'value'    => $result['html']
                ]
            ]
        ];
        
        return $result;
    }
}