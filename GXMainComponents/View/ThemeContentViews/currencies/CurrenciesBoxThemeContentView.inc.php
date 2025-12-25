<?php
/* --------------------------------------------------------------
   CurrenciesBoxThemeContentView.inc.php 2023-05-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(currencies.php,v 1.16 2003/02/12); www.oscommerce.com
   (c) 2003	 nextcommerce (currencies.php,v 1.11 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: currencies.php 1262 2005-09-30 10:00:32Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

class CurrenciesBoxThemeContentView extends ThemeContentView
{
    protected $coo_xtc_price;
    protected $requestType;
    protected $getArray                = [];
    protected $hiddenGetVariables      = '';
    protected $currenciesArray         = [];
    protected $hiddenGetVariablesArray = [];
    protected $formMethod              = '';
    protected $currenciesCount         = 0;
    protected $getVariables            = '';
    
    
    public function __construct()
    {
        parent::__construct();
        $this->set_content_template('layout_secondary_navigation_currencies_dropdown.html');
        $this->formMethod = 'get';
    }
    
    
    public function prepare_data()
    {
        $this->_setHiddenGetVariables();
        $this->_setCurrenciesArray();
        $this->_setCurrentCurrency();
        $this->_setCurrenciesData();

    }
    
    
    public function setXtcPrice(xtcPrice $p_coo_xtc_price)
    {
        $this->coo_xtc_price = $p_coo_xtc_price;
    }
    
    
    public function setRequestType($p_request_type)
    {
        $this->requestType = (string)$p_request_type;
    }
    
    
    public function setGetArray(array $p_get_array)
    {
        $this->getArray = $p_get_array;
    }
    
    
    protected function _setHiddenGetVariables()
    {
        if (!is_array($this->getArray)) {
            return;
        }
        
        $array = array_filter($this->getArray,
            function ($k) { return !in_array($k, ['currency', xtc_session_name(), 'x', 'y']); },
                              ARRAY_FILTER_USE_KEY);
        
        if (count($array)) {
            $this->getVariables .= '&' . http_build_query($array, '', '&', PHP_QUERY_RFC3986);
        }
    }
    
    
    protected function _setCurrenciesArray()
    {
        if (sizeof($this->coo_xtc_price->currencies) > 0) {
            foreach ($this->coo_xtc_price->currencies as $kCurrency => $vCurrency) {
                $c_key = htmlentities_wrapper($kCurrency);
                $this->currenciesCount++;
                $this->currenciesArray[] = [
                    'id'   => $c_key,
                    'text' => htmlentities_wrapper($vCurrency['title']),
                    'link' => xtc_href_link(basename(gm_get_env_info('PHP_SELF')),
                                            'currency=' . $c_key . $this->getVariables,
                                            $this->requestType)
                ];
            }
        }
    }
    
    
    protected function _setCurrenciesData()
    {
        // don't show box if there's only 1 currency
        if ($this->currenciesCount > 1) {
            $this->set_content_data('currencies_data', $this->currenciesArray);
        } else {
            $this->set_content_data('currencies_data', []);
        }
    }
    
    
    protected function _setCurrentCurrency()
    {
        $this->set_content_data('CURRENT_CURRENCY', $_SESSION['currency']);
    }
    
    
    /**
     * @deprecated
     */
    protected function _setSessionId()
    {
        // deprecated
    }
    
    
    /**
     * @deprecated
     */
    protected function _setHiddenGetVariablesData()
    {
        // deprecated
    }
    
    
    /**
     * @deprecated
     */
    protected function _setFormParams()
    {
        // deprecated
    }
    
    
    /**
     * @deprecated
     */
    protected function _setFormId()
    {
        // deprecated
    }
    
    
    /**
     * @deprecated
     */
    protected function _setFormActionUrl()
    {
        // deprecated
    }
    
    
    /**
     * @deprecated
     */
    protected function _setFormMethod()
    {
        // deprecated
    }
    
    
    public function set_currency_dropdown_template()
    {
        $this->set_content_template('layout_secondary_navigation_currencies_dropdown.html');
    }
}
