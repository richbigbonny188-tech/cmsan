<?php
/* --------------------------------------------------------------
   AccountHistoryThemeContentView.inc.php 2018-11-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(account_history_info.php,v 1.97 2003/05/19); www.oscommerce.com 
   (c) 2003	 nextcommerce (account_history_info.php,v 1.17 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: account_history.php 1309 2005-10-17 08:01:11Z mz $)

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

// include needed functions
require_once(DIR_FS_INC . 'xtc_date_long.inc.php');

class AccountHistoryThemeContentView extends ThemeContentView
{
    protected $customerId;
    protected $languageId;
    protected $page;
    protected $db;
    
    
    public function __construct()
    {
        parent::__construct();
        $this->set_content_template('account_history.html');
        $this->set_flat_assigns(true);
        $this->set_caching_enabled(false);
        $this->db   = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $this->page = (int)($this->v_env_get_array['page'] ?? 1);
    }
    
    
    public function prepare_data()
    {
        $limit         = (int)MAX_DISPLAY_ORDER_HISTORY;
        $totalItems    = $this->_getTotalOrderCount();
        $pageParameter = 'page';
        $pager         = ExtendedInformationPager::createExtendedInformationPager($this->page,
                                                                                  $limit,
                                                                                  $totalItems,
                                                                                  $pageParameter);
        
        $navigationUrl = splitPageResults::get_navigation_url();
        
        $orderData     = $this->_getOrderData($pager);
        $data          = [
            'orders'         => $this->_prepareOrderData($orderData),
            'pager'          => $pager,
            'navigation_url' => $navigationUrl,
            'page_param'     => strpos($navigationUrl, '?') !== false ? '&page=' : '?page=',
            'pages'          => $pager->getPages()
        ];
        
        foreach ($data as $key => $value) {
            $this->set_content_data($key, $value);
        }
    }
    
    
    protected function _getOrderData(Pager $pager)
    {
        $db                = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $productsSubSelect = $db->select('COUNT(*) AS `count`')
            ->from('orders_products as op')
            ->where('op.orders_id = o.orders_id')
            ->get_compiled_select();
        
        return $db->select('o.orders_id')
            ->select('o.date_purchased')
            ->select('o.delivery_name')
            ->select('o.billing_name')
            ->select('ot.text as order_total')
            ->select('s.orders_status_name')
            ->select('(' . $productsSubSelect . ') AS product_count',
                     false)
            ->from('orders AS o')
            ->from('orders_total AS ot')
            ->from('orders_status AS s')
            ->from('customers_info AS ci')
            ->where('o.customers_id', $this->customerId)
            ->where('o.orders_id = ot.orders_id')
            ->where('ot.class', 'ot_total')
            ->where('o.orders_status = s.orders_status_id')
            ->where('s.language_id', $this->languageId)
            ->where('o.customers_id = ci.customers_info_id')
            ->where('o.date_purchased >= ci.customers_info_date_account_created')
            ->order_by('orders_id', 'desc')
            ->limit($pager->perPage(), $pager->offset())
            ->get()
            ->result_array();
    }
    
    
    protected function _getTotalOrderCount()
    {
        $result = $this->db->select('COUNT(*) as count')
            ->from('orders AS o')
            ->from('orders_total AS ot')
            ->from('orders_status AS s')
            ->from('customers_info AS ci')
            ->where('o.customers_id', $this->customerId)
            ->where('o.orders_id = ot.orders_id')
            ->where('ot.class', 'ot_total')
            ->where('o.orders_status = s.orders_status_id')
            ->where('s.language_id',
                    $this->languageId)
            ->where('o.customers_id = ci.customers_info_id')
            ->where('o.date_purchased >= ci.customers_info_date_account_created')
            ->order_by('o.orders_id', 'desc')
            ->get()
            ->row_array();
        
        return $result ? (int)$result['count'] : 0;
    }
    
    
    protected function _prepareOrderData(array $orderData)
    {
        foreach ($orderData as &$dataSet) {
            $dataSet['date_purchased'] = xtc_date_long($dataSet['date_purchased']);
            $dataSet['order_total']    = strip_tags($dataSet['order_total']);
            $dataSet['btn_url']        = 'account_history_info.php?page=' . $this->page . '&order_id='
                                         . $dataSet['orders_id'];
        }
        
        return $orderData;
    }
    
    
    /**
     * @param int $p_customerId
     */
    public function setCustomerId($p_customerId)
    {
        $this->customerId = (int)$p_customerId;
    }
    
    
    /**
     * @param int $p_languageId
     */
    public function setLanguageId($p_languageId)
    {
        $this->languageId = (int)$p_languageId;
    }
    
    
    /**
     * @param string $p_page
     */
    public function setPage($p_page)
    {
        $this->page = (int)$p_page;
    }
}
