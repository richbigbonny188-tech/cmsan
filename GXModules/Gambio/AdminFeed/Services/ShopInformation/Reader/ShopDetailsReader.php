<?php
/* --------------------------------------------------------------
   ShopDetailsReader.php 2018-08-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\AdminFeed\Services\ShopInformation\Reader;

use CI_DB_query_builder;
use Gambio\AdminFeed\Services\ShopInformation\Settings;

/**
 * Class ShopDetailsReader
 *
 * @package Gambio\AdminFeed\Services\ShopInformation\Reader
 */
class ShopDetailsReader
{
    /**
     * @var Settings
     */
    private $settings;
    
    
    /**
     * @var CI_DB_query_builder
     */
    private $db;
    
    
    /**
     * ShopDetailsReader constructor.
     *
     * @param Settings            $settings
     * @param CI_DB_query_builder $db
     */
    public function __construct(Settings $settings, CI_DB_query_builder $db)
    {
        $this->settings = $settings;
        $this->db       = $db;
    }
    
    
    /**
     * Returns the shop version.
     *
     * @return string
     */
    public function getVersion()
    {
        include $this->settings->getBaseDirectory() . 'release_info.php';
        
        return isset($gx_version) ? $gx_version : '';
    }
    
    
    /**
     * Returns the shop URL.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->settings->getHttpServer() . $this->settings->getShopDirectory();
    }
    
    
    /**
     * Returns the shop key.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->settings->getShopKey();
    }
    
    
    /**
     * Returns a list of available languages in the shop.
     *
     * @return array
     */
    public function getLanguages()
    {
        $return = [];
        
        $languages = $this->db->select('code')
            ->from('languages')
            ->where('status', '1')
            ->order_by('code')
            ->get()
            ->result_array();
        foreach ($languages as $language) {
            $return[] = $language['code'];
        }
        
        return $return;
    }
    
    
    /**
     * Returns the default language of the shop.
     *
     * @return string
     */
    public function getDefaultLanguage()
    {
        return $this->settings->getDefaultLanguage();
    }
    
    
    /**
     * Returns a list of available countries in the shop.
     *
     * @return array
     */
    public function getCountries()
    {
        $return = [];
        
        $countries = $this->db->select('countries_iso_code_2')
            ->from('countries')
            ->where('status', '1')
            ->order_by('countries_iso_code_2')
            ->get()
            ->result_array();
        foreach ($countries as $country) {
            $return[] = $country['countries_iso_code_2'];
        }
        
        return $return;
    }
}