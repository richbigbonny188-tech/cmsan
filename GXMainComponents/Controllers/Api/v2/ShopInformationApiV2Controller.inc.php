<?php
/* --------------------------------------------------------------
   ShopInformationApiV2Controller.inc.php 2018-11-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
if (!function_exists('xtc_get_country_name')) {
    require_once DIR_FS_INC . 'xtc_get_country_name.inc.php';
}

if (!function_exists('xtc_get_zone_name')) {
    require_once DIR_FS_INC . 'xtc_get_zone_name.inc.php';
}

MainFactory::load_class('HttpApiV2Controller');

class ShopInformationApiV2Controller extends HttpApiV2Controller
{
    /**
     * @api        {get} /shop_information Get Shop Information
     * @apiVersion 2.4.0
     * @apiName    GetShopInformation
     * @apiGroup   ShopInformation
     *
     * @apiDescription
     * Returns shop information like shop url, shop name, shop owner address data, shop template name and shop version.
     *
     * @apiExample {curl} Get Shop Information
     *             curl -i --user admin@example.org:12345 https://example.org/api.php/v2/shop_information
     */
    public function get()
    {
        $response = [
            'url'         => HTTP_SERVER . DIR_WS_CATALOG,
            'shopName'    => STORE_NAME,
            'owner'       => STORE_OWNER,
            'company'     => COMPANY_NAME,
            'firstname'   => TRADER_FIRSTNAME,
            'lastname'    => TRADER_NAME,
            'street'      => TRADER_STREET,
            'houseNumber' => TRADER_STREET_NUMBER,
            'postcode'    => TRADER_ZIPCODE,
            'city'        => TRADER_LOCATION,
            'state'       => xtc_get_zone_name(STORE_COUNTRY, STORE_ZONE, ''),
            'country'     => xtc_get_country_name(STORE_COUNTRY),
            'telephone'   => TRADER_TEL,
            'fax'         => TRADER_FAX,
            'email'       => STORE_OWNER_EMAIL_ADDRESS,
            'zoneId'      => STORE_ZONE,
            'countryId'   => STORE_COUNTRY,
            'template'    => StaticGXCoreLoader::getThemeControl()->getCurrentTheme(),
            'shopVersion' => gm_get_conf('INSTALLED_VERSION')
        ];
        
        $this->_writeResponse($response);
    }
}
