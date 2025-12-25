<?php
/* --------------------------------------------------------------
   CountriesBoxThemeContentView.inc.php 2018-11-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CountriesBoxThemeContentView
 */
class CountriesBoxThemeContentView extends ThemeContentView
{
    /**
     * @var int $languageId
     */
    protected $languageId;
    
    /**
     * @var CustomerCountryIso2Interface
     */
    protected $customerCountryIsoCode;
    
    
    public function __construct()
    {
        parent::__construct();
        $this->set_content_template('layout_secondary_navigation_countries_dropdown.html');
    }
    
    
    public function prepare_data()
    {
        $this->set_content_data('country_data', $this->buildCountryArray());
        $this->set_content_data('URL', $this->buildUrl());
        
        if ($this->customerCountryIsoCode === null) {
            $query  = 'SELECT `countries_iso_code_2` FROM `countries` WHERE `countries_id` = ' . (int)STORE_COUNTRY;
            $result = xtc_db_query($query);
            
            if (xtc_db_num_rows($result)) {
                $row                          = $result->fetch_array();
                $iso_code                     = $row['countries_iso_code_2'];
                $this->customerCountryIsoCode = $iso_code;
            }
        }
        
        $this->set_content_data('SELECTED_COUNTRY', (string)$this->customerCountryIsoCode);
    }
    
    
    /**
     * @param int $p_languageId
     */
    public function setLanguageId($p_languageId)
    {
        $this->languageId = (int)$p_languageId;
    }
    
    
    /**
     * @param CustomerCountryIso2Interface $isoCode
     */
    public function setCustomerCountryIsoCode(CustomerCountryIso2Interface $isoCode)
    {
        $this->customerCountryIsoCode = $isoCode;
    }
    
    
    /**
     * @return array
     */
    protected function buildCountryArray()
    {
        /* @var Countries $countries */
        $countries    = MainFactory::create_object('Countries', [$this->languageId, true, true]);
        $countryArray = $countries->get_countries_array();
        
        return $countryArray;
    }
    
    
    /**
     * @return string
     */
    protected function buildUrl()
    {
        $url = htmlspecialchars_wrapper(gm_get_env_info('REQUEST_URI'));
        $url = preg_replace('/(\?|&amp;)switch_country=[A-Z]{2}/', '', $url);
        
        return $url;
    }
}
