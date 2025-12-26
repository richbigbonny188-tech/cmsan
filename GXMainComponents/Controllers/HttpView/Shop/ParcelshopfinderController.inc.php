<?php
/* --------------------------------------------------------------
	ParcelshopfinderController.inc.php 2024-01-25
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2024 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * Class ParcelshopfinderController
 *
 * @extends    HttpViewController
 * @category   System
 * @package    HttpViewControllers
 */
class ParcelshopfinderController extends HttpViewController
{
    /**
     * @var ConfigurationStorage
     */
    protected $configuration;
    
    /**
     * @var LanguageTextManager
     */
    protected $languageTextManager;
    
    
    protected function init()
    {
        $this->languageTextManager = MainFactory::create('LanguageTextManager',
                                                         'parcelshopfinder',
                                                         $_SESSION['languages_id']);
        $this->configuration       = MainFactory::create('ConfigurationStorage', 'modules/shipping/parcelshopfinder');
        $this->contentView->set_template_dir(DIR_FS_CATALOG . StaticGXCoreLoader::getThemeControl()
                                                 ->getThemeHtmlPath());
    }
    
    
    protected function getRedirectToSearchByDefaultAddress(string $filter): string
    {
        $db = StaticGXCoreLoader::getDatabaseQueryBuilder();
        
        $defaultAddress = $db->get_where('address_book',
                                         [
                                             'address_book_id' => $_SESSION['customer_default_address_id'],
                                         ])->row();
        $countryService = StaticGXCoreLoader::getService('Country');
        $addressCountry = $countryService->getCountryById(MainFactory::create('IdType',
                                                                              (int)$defaultAddress->entry_country_id));
        if (empty($defaultAddress->entry_house_number)) {
            $splitStreet = $this->splitStreet($defaultAddress->entry_street_address);
        } else {
            $splitStreet = [
                'street'   => $defaultAddress->entry_street_address,
                'house_no' => $defaultAddress->entry_house_number,
            ];
        }
        $street        = $splitStreet['street'];
        $house         = $splitStreet['house_no'];
        $zip           = $defaultAddress->entry_postcode;
        $city          = $defaultAddress->entry_city;
        $country       = $addressCountry->getIso2();
        $redirectQuery = [
            'do'               => 'Parcelshopfinder',
            'checkout_started' => ((int)$this->_getQueryParameter('checkout_started') === 1) ? '1' : '0',
            'street'           => $street,
            'house'            => $house,
            'zip'              => $zip,
            'city'             => $city,
            'country'          => (string)$country,
            'filter'           => $filter,
        ];
        $redirectUrl   = xtc_href_link('shop.php',
                                       http_build_query($redirectQuery, '', '&'),
                                       'SSL',
                                       true,
                                       true,
                                       false,
                                       false);
        
        return $redirectUrl;
    }
    
    
    public function actionDefault()
    {
        if (empty($_SESSION['customer_id'])) {
            return MainFactory::create('RedirectHttpControllerResponse', xtc_href_link(FILENAME_DEFAULT, '', 'SSL'));
        }
        
        $street  = $this->_getQueryParameter('street');
        $house   = $this->_getQueryParameter('house');
        $zip     = $this->_getQueryParameter('zip');
        $city    = $this->_getQueryParameter('city');
        $country = $this->_getQueryParameter('country');
        $country = strtoupper(substr(trim($country), 0, 2));
        $filter  = $this->_getQueryParameter('filter');
        $filter  = in_array($filter, ['packstations', 'offices', 'both'], true) ? $filter : 'both';
        
        if (empty($country)) {
            $redirectUrl = $this->getRedirectToSearchByDefaultAddress($filter);
            
            return MainFactory::create('RedirectHttpControllerResponse', $redirectUrl);
        }
        
        $tplData = [
            'form_action'      => xtc_href_link('shop.php', '', 'SSL'),
            'do'               => 'Parcelshopfinder',
            'street'           => $street,
            'house'            => $house,
            'zip'              => $zip,
            'city'             => $city,
            'country'          => $country,
            'filter'           => $filter,
            'countries'        => [['countries_iso_code_2' => 'DE']],
            'checkout_started' => (int)$this->_getQueryParameter('checkout_started') === 1 ? '1' : '0',
            'search_result'    => $this->_getSearchResultOutput($country, $city, $zip, $house, $street, $filter),
        ];
        
        if (StaticGXCoreLoader::getThemeControl()->isThemeSystemActive()) {
            $mainContent = $this->_render('address_book_parcelshopfinder.html', $tplData);
        } else {
            $mainContent = $this->_render('parcelshopfinder.html', $tplData);
        }
        
        $GLOBALS['breadcrumb']->add($this->languageTextManager->get_text('parcelshopfinder_breadcrumb'),
                                    xtc_href_link('shop.php',
                                                  xtc_get_all_get_params(['do']) . '&do=Parcelshopfinder',
                                                  'SSL'));
        
        $layoutContentControl = MainFactory::create_object('LayoutContentControl');
        $layoutContentControl->set_data('GET', $this->_getQueryParametersCollection()->getArray());
        $layoutContentControl->set_data('POST', $this->_getPostDataCollection()->getArray());
        $layoutContentControl->set_('coo_breadcrumb', $GLOBALS['breadcrumb']);
        $layoutContentControl->set_('coo_product', $GLOBALS['product']);
        $layoutContentControl->set_('coo_xtc_price', $GLOBALS['xtPrice']);
        $layoutContentControl->set_('c_path', $GLOBALS['cPath']);
        $layoutContentControl->set_('main_content', $mainContent);
        $layoutContentControl->set_('request_type', $GLOBALS['request_type']);
        $layoutContentControl->proceed();
        
        $redirectUrl = $layoutContentControl->get_redirect_url();
        if (!empty($redirectUrl)) {
            return MainFactory::create('RedirectHttpControllerResponse', $redirectUrl);
        }
        
        return MainFactory::create('HttpControllerResponse', $layoutContentControl->get_response());
    }
    
    
    protected function _getSearchResultOutput($country, $city, $zip, $house, $street, $filter = 'both'): string
    {
        $firstname  = $this->_getQueryParameter('firstname') ? : $_SESSION['customer_first_name'];
        $lastname   = $this->_getQueryParameter('lastname') ? : $_SESSION['customer_last_name'];
        $postnumber = $this->_getQueryParameter('postnumber') ? : '';
        $postnumber = preg_replace('/[^\d]/', '', $postnumber);
        $error      = $this->_getQueryParameter('error');
        
        if (empty($country)) {
            return '';
        }
        
        $throttleWindow      = 3600;
        $throttleLimit       = 10;
        $now                 = time();
        $requestTimestamps   = $_SESSION['ParcelshopfinderRequests'] ?? [];
        $throttleWindowStart = $now - $throttleWindow;
        $requestsInWindow    = array_filter($requestTimestamps,
            static function ($timestamp) use ($throttleWindowStart) {
                return $timestamp > $throttleWindowStart;
            });
        if (count($requestsInWindow) >= $throttleLimit) {
            $locationsList = [];
        } else {
            $psf                   = MainFactory::create('ParcelShopFinder');
            $locationFinderAddress = MainFactory::create('LocationFinderAddress',
                                                         $country,
                                                         $city,
                                                         $zip,
                                                         "$street $house");
            $limit                 = (int)$this->configuration->get('maximum_list_entries');
            if ($limit === 0) {
                $limit = 10;
            }
            $servicePoints      = $filter === 'both' || $filter === 'offices';
            $postOffices        = $filter === 'both' || $filter === 'offices';
            $lockers            = $filter === 'both' || $filter === 'packstations';
            $locationsList      = $psf->findLocationsByAddress($locationFinderAddress,
                                                               15000,
                                                               $limit,
                                                               $servicePoints,
                                                               $postOffices,
                                                               $lockers);
            $requestsInWindow[] = $now;
        }
        $_SESSION['ParcelshopfinderRequests'] = $requestsInWindow;
        
        if (!empty($error)) {
            if ($error === 'invalid_postnumber') {
                $errorMessage = $this->languageTextManager->get_text('error_invalid_postnumber');
            }
        }
        
        $mapUrl = '';
        if ($this->configuration->get('google_map_type') === 'static') {
            $mapUrl = $this->makeMapUrl($locationsList);
        }
        $markerData = [];
        if ($this->configuration->get('google_map_type') === 'dynamic') {
            $markerData = $this->makeMarkersJson($locationsList);
        }
        
        $address_readable = '';
        $address_readable .= !empty($street) ? $street . ' ' . $house . ', ' : '';
        $address_readable .= $zip . ' ' . $city . ' (' . $country . ')';
        
        $tplData = [
            'error_message'            => $errorMessage ?? '',
            'customer'                 => [
                'firstname'  => $firstname,
                'lastname'   => $lastname,
                'postnumber' => $postnumber,
            ],
            'search'                   => base64_encode(serialize([$street, $house, $zip, $city, $country])),
            'address'                  => $address_readable,
            'country'                  => $country,
            'filter'                   => $filter,
            'locationsList'            => $locationsList,
            'ooTranslations'           => [
                'http://schema.org/Monday'    => $this->languageTextManager->get_text('openinghours_day_mo'),
                'http://schema.org/Tuesday'   => $this->languageTextManager->get_text('openinghours_day_tu'),
                'http://schema.org/Wednesday' => $this->languageTextManager->get_text('openinghours_day_we'),
                'http://schema.org/Thursday'  => $this->languageTextManager->get_text('openinghours_day_th'),
                'http://schema.org/Friday'    => $this->languageTextManager->get_text('openinghours_day_fr'),
                'http://schema.org/Saturday'  => $this->languageTextManager->get_text('openinghours_day_sa'),
                'http://schema.org/Sunday'    => $this->languageTextManager->get_text('openinghours_day_su'),
            ],
            'google_map_type'          => $this->configuration->get('google_map_type'),
            'mapUrl'                   => $mapUrl,
            'markerData'               => $markerData,
            'googleApiKey'             => $this->configuration->get('google_api_key'),
            'form_action_new_ab_entry' => xtc_href_link('shop.php', 'do=Parcelshopfinder/AddAddressBookEntry', 'SSL'),
            'backlink'                 => xtc_href_link('shop.php', 'do=Parcelshopfinder', 'SSL'),
            'checkout_started'         => ($this->_getQueryParameter('checkout_started') == 1) ? '1' : '0',
            'searchAddress'            => sprintf('%s %s, %s %s, %s', $street, $house, $zip, $city, $country),
        ];
        
        return $this->_render('address_book_parcelshopfinder_result.html', $tplData);
    }
    
    
    public function actionAddAddressBookEntry()
    {
        if (empty($_SESSION['customer_id'])) {
            return MainFactory::create('RedirectHttpControllerResponse', xtc_href_link(FILENAME_DEFAULT, '', 'SSL'));
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $firstname        = $this->_getPostData('firstname');
            $lastname         = $this->_getPostData('lastname');
            $postnumber       = $this->_getPostData('postnumber');
            $street_address   = $this->_getPostData('street_address');
            $house_number     = $this->_getPostData('house_number');
            $additional_info  = $this->_getPostData('additional_info');
            $postcode         = $this->_getPostData('postcode');
            $city             = $this->_getPostData('city');
            $country_iso2     = $this->_getPostData('country');
            $country_iso2     = strtolower(substr(trim($country_iso2), 0, 2));
            $checkout_started = $this->_getPostData('checkout_started') == 1;
            
            $addressClass = 'psf/undefined';
            if ($country_iso2 === 'de') {
                if (stripos($street_address, 'packstation') !== false) {
                    $addressClass = 'packstation_2';
                }
                if (stripos($street_address, 'filiale') !== false) {
                    $addressClass = 'postfiliale_2';
                }
                if (stripos($street_address, 'paketshop') !== false
                    || stripos($street_address,
                               'parcel shop') !== false) {
                    $addressClass = 'postfiliale_2';
                }
                
                $postnumber = abs(filter_var($postnumber, FILTER_SANITIZE_NUMBER_INT));
                if (empty($postnumber) || $this->isValidPostnummer($postnumber) !== true) {
                    $psfParams = [
                        'firstname'       => $firstname,
                        'lastname'        => $lastname,
                        'postnumber'      => $postnumber,
                        'additional_info' => $additional_info,
                        'error'           => 'invalid_postnumber',
                    ];
                    
                    return MainFactory::create('RedirectHttpControllerResponse',
                                               xtc_href_link('shop.php',
                                                             'do=Parcelshopfinder&' . http_build_query($psfParams),
                                                             'SSL',
                                                             true,
                                                             true,
                                                             false,
                                                             false));
                }
                $additional_info = sprintf('Postnummer %s', $postnumber);
            } else {
                $postnumber   = '';
                $addressClass = 'parcelshop';
            }
            
            $country     = $this->findCountryByIso2($country_iso2);
            $countryZone = MainFactory::create('CustomerCountryZone',
                                               new IdType(0),
                                               MainFactory::create('CustomerCountryZoneName', ''),
                                               MainFactory::create('CustomerCountryZoneIsoCode', ''));
            
            $addressBlock = MainFactory::create('AddressBlock',
                                                MainFactory::create('CustomerGender', ''),
                                                MainFactory::create('CustomerFirstname', $firstname),
                                                MainFactory::create('CustomerLastname', $lastname),
                                                MainFactory::create('CustomerCompany', ''),
                                                MainFactory::create('CustomerB2BStatus',
                                                                    (bool)$_SESSION['customer_b2b_status']),
                                                MainFactory::create('CustomerStreet', $street_address),
                                                MainFactory::create('CustomerHouseNumber', $house_number),
                                                MainFactory::create('CustomerAdditionalAddressInfo', $additional_info),
                                                MainFactory::create('CustomerSuburb', ''),
                                                MainFactory::create('CustomerPostcode', $postcode),
                                                MainFactory::create('CustomerCity', $city),
                                                $country,
                                                $countryZone);
            
            $customerService    = StaticGXCoreLoader::getService('Customer');
            $customer           = $customerService->getCustomerById(MainFactory::create('IdType',
                                                                                        $_SESSION['customer_id']));
            $addressBookService = StaticGXCoreLoader::getService('AddressBook');
            $newAddress         = $addressBookService->createNewAddress($addressBlock, $customer);
            $newAddress->setAddressClass(MainFactory::create('AddressClass', $addressClass));
            $addressBookService->updateCustomerAddress($newAddress);
            if ($checkout_started === true) {
                $_SESSION['sendto'] = $newAddress->getId();
            }
        }
        
        if ($checkout_started === true) {
            return MainFactory::create('RedirectHttpControllerResponse',
                                       xtc_href_link('checkout_shipping.php', '', 'SSL'));
        }
    
        return MainFactory::create('RedirectHttpControllerResponse', xtc_href_link('address_book.php', '', 'SSL'));
    }
    
    public function actionValidatePostnumber()
    {
        $postnumber = $this->_getQueryParameter('postnumber');
        $result     = [
            'postnumberIsValid' => !empty($postnumber) && is_numeric($postnumber)
                                   && $this->isValidPostnummer($postnumber),
        ];
        
        return MainFactory::create('JsonHttpControllerResponse', $result);
    }
    
    
    /* ================================================================================================================================= */
    
    /**
     * checks validity of a DHL post number
     *
     * @param $postnum string post number (up to 10 digits)
     *
     * @return bool true if $postnum represents a (syntactically) valid post number
     */
    public function isValidPostnummer(string $postnum): bool
    {
        $postnum = sprintf('%010d', $postnum);
        $sum1    = 0;
        for ($i = 8; $i >= 0; $i -= 2) {
            $sum1 += (int)$postnum[$i];
        }
        $sum2 = 0;
        for ($j = 7; $j >= 1; $j -= 2) {
            $sum2 += (int)$postnum[$j];
        }
        $sum12    = ($sum1 * 4) + ($sum2 * 9);
        $checknum = (10 - ($sum12 % 10)) % 10;
    
        return (int)$postnum[9] === $checknum;
    }
    
    
    /**
     * finds a country by its 2-letter ISO code
     *
     * @param $iso2 string 2-letter ISO code
     *
     * @return CustomerCountry
     * @todo To be refactored as soon as CountryService::findCountryByIso2() becomes available
     *
     */
    protected function findCountryByIso2($iso2)
    {
        $iso2       = strtolower(substr($iso2, 0, 2));
        $db         = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $countryRow = $db->get_where('countries', ['countries_iso_code_2' => $iso2])->row();
        if ($countryRow === null) {
            throw new Exception('Invalid country code');
        }
        $countries_id   = $countryRow->countries_id;
        $countryService = StaticGXCoreLoader::getService('Country');
        $country        = $countryService->getCountryById(MainFactory::create('IdType', $countries_id));
        
        return $country;
    }
    
    
    protected function makeMarkersJson(array &$locations)
    {
        $iconBaseUrl     = GM_HTTP_SERVER . DIR_WS_CATALOG . StaticGXCoreLoader::getThemeControl()->getThemeImagePath()
                           . 'icons/';
        $iconPackstation = $iconBaseUrl . 'packstation.png';
        $iconFiliale     = $iconBaseUrl . 'postfiliale.png';
        $iconPaketshop   = $iconBaseUrl . 'paketshop.png';
        
        $mapLabels   = [
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'Q',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'X',
            'Y',
            'Z',
            '1',
            '2',
            '3',
            '4',
            '5',
            '6',
            '7',
            '8',
            '9',
            'α',
            'β',
            'γ',
            'δ',
            'ε',
            'φ',
            'γ',
            'ψ',
            'ι',
            'θ',
            'κ',
            'λ',
            'μ',
            'ν',
            'π',
            'ϕ',
            'ρ',
            'σ',
            'τ',
            'ω',
            '•',
            '•',
            '•',
            '•',
            '•',
            '•',
            '•',
            '•',
            '•',
            '•',
            '•',
            '•',
            '•',
            '•',
            '•',
            '•',
            '•',
            '•',
            '•',
            '•',
            '•',
            '•',
            '•',
            '•',
            '•',
            '•',
        ];
        $markersJson = [];
        $labelIndex  = -1;
        $lastLat     = 0;
        $lastLng     = 0;
        foreach ($locations as $locationsIndex => $location) {
            $title = '';
            $icon  = '';
            
            if ($location['location']['keyword'] === 'Postfiliale') {
                $title .= 'Postfiliale' . ' ' . $location['location']['keywordId'];
            } elseif ($location['location']['keyword'] === 'Packstation') {
                $title .= 'Packstation' . ' ' . $location['location']['keywordId'];
            }
            
            if (!empty($location['name'])) {
                $title .= ' (' . $location['name'] . ')';
            }
            
            if ($location['location']['keyword'] === 'Postfiliale') {
                $icon = $iconFiliale;
            } elseif ($location['location']['keyword'] === 'Packstation') {
                $icon = $iconPackstation;
            }
            
            if ($location['place']['geo']['latitude'] === $lastLat
                && $location['place']['geo']['longitude'] === $lastLng) {
                $location['place']['geo']['latitude']  -= 0.00015;
                $location['place']['geo']['longitude'] += 0.0002;
            }
            
            $labelIndex++;
            
            if (!array_key_exists($labelIndex, $mapLabels)) {
                break;
            }
            
            $markersJson[] = [
                'position' => [
                    'lat' => $location['place']['geo']['latitude'],
                    'lng' => $location['place']['geo']['longitude'],
                ],
                'title'    => $title,
                'label'    => $mapLabels[$labelIndex],
                'icon'     => $icon,
            ];
            
            $locations[$locationsIndex]['mapMarkerLabel'] = $mapLabels[$labelIndex];
            $lastLat                                      = $location['place']['geo']['latitude'];
            $lastLng                                      = $location['place']['geo']['longitude'];
        }
        
        return json_encode($markersJson);
    }
    
    
    /**
     * Generates img URL for static Google map integration.
     *
     * @param array $locationList
     *
     * @return false|string
     */
    protected function makeMapUrl(array &$locationList)
    {
        $googleApiKey    = $this->configuration->get('google_api_key');
        $googleUrlSecret = $this->configuration->get('google_url_signature_secret');
        
        if (empty($googleApiKey) || empty($googleUrlSecret)) {
            return false;
        }
        
        $googleUrlSecret = strtr($googleUrlSecret, ['-' => '+', '_' => '/']);
        $googleUrlSecret = base64_decode($googleUrlSecret);
        
        $mapLabels  = [
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'Q',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'X',
            'Y',
            'Z',
        ];
        $mapMarkers = [];
        
        foreach ($locationList as $locationIndex => $location) {
            if (array_key_exists($locationIndex, $mapLabels)) {
                $mapMarkers[] = [
                    'color' => 'yellow',
                    'label' => $mapLabels[$locationIndex],
                    'lat'   => $location['place']['geo']['latitude'],
                    'lon'   => $location['place']['geo']['longitude'],
                ];
                
                $locationList[$locationIndex]['mapMarkerLabel'] = $mapLabels[$locationIndex];
            } else {
                break;
            }
        }
        
        $mapMarkersArray       = array_map(function ($markerData) {
            return 'markers=' . rawurlencode(sprintf('color:%s|label:%s|%s,%s',
                                                     $markerData['color'],
                                                     $markerData['label'],
                                                     $markerData['lat'],
                                                     $markerData['lon']));
        },
            $mapMarkers);
        $mapMarkersQueryString = implode('&', $mapMarkersArray);
        
        $mapParameters = [
            'zoom'    => '13',
            'size'    => '640x360',
            'scale'   => '2',
            'maptype' => 'roadmap',
            'key'     => $googleApiKey,
        ];
        /*
        $mapUrl        = sprintf('%s?%s&%s',
                                 '/maps/api/staticmap',
                                 http_build_query($mapParameters, '', '&'),
                                 $mapMarkersQueryString);
        */
        $mapUrl    = "/maps/api/staticmap?{$mapMarkersQueryString}&" . http_build_query($mapParameters, '', '&');
        $signature = hash_hmac('sha1', $mapUrl, $googleUrlSecret, true);
        $signature = base64_encode($signature);
        $signature = strtr($signature, ['+' => '-', '/' => '_']);
        $mapUrl    = 'https://maps.googleapis.com' . $mapUrl . '&signature=' . $signature;
        
        return $mapUrl;
    }
    
    /* ================================================================================================================================= */
    
    /**
     * Heuristically splits up a street address into its component street name and house number
     *
     * @param string
     *
     * @return array with keys 'street' and 'house_no'
     */
    protected function splitStreet($street_address)
    {
        $street_address = trim($street_address);
        $splitStreet    = [
            'street'   => $street_address,
            'house_no' => '',
        ];
        $matches        = [];
        if (preg_match('_^(\d.*?)\s(.+)_', $street_address, $matches) === 1) {
            $splitStreet['street']   = $matches[2];
            $splitStreet['house_no'] = $matches[1];
        } else {
            if (preg_match('_(.+?)\s?(\d.*)_', $street_address, $matches) === 1) {
                $splitStreet['street']   = $matches[1];
                $splitStreet['house_no'] = $matches[2];
            }
        }
        
        return $splitStreet;
    }
}
