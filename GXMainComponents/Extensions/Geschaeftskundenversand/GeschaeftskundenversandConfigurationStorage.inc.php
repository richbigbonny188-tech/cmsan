<?php
/* --------------------------------------------------------------
	GeschaeftskundenversandConfigurationStorage.inc.php 2023-06-14
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2023 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

use Doctrine\DBAL\Connection;
use Gambio\Core\Application\Application;
use Gambio\Core\Application\ValueObjects\Url;
use Gambio\Core\Configuration\Compatibility\Repositories\Storage\ConfigurationStorageReader;
use Gambio\Core\Configuration\Services\ConfigurationService;

/**
 * Class GeschaeftskundenversandConfigurationStorage
 *
 * @extends    ConfigurationStorage
 * @category   System
 * @package    Extensions
 * @subpackage Geschaeftskundenversand
 */
class GeschaeftskundenversandConfigurationStorage extends ConfigurationStorage
{
    /**
     * namespace inside the configuration storage
     */
    public const CONFIG_STORAGE_NAMESPACE = 'modules/shipping/geschaeftskundenversand';
    
    public const MAJOR_VERSION = 3;
    public const MINOR_VERSION = 5;
    public const BUILD         = 0;
    
    /**
     * array holding default values to be used in absence of configured values
     */
    protected $default_configuration;
    
    
    /**
     * GeschaeftskundenversandConfigurationStorage constructor.
     *
     * Initializes default configuration.
     */
    public function __construct()
    {
        parent::__construct(self::CONFIG_STORAGE_NAMESPACE);
        $this->setDefaultConfiguration();
    }
    
    
    /**
     * fills $default_configuration with initial values
     */
    protected function setDefaultConfiguration()
    {
        /** @var Url $url */
        $url = LegacyDependencyContainer::getInstance()->get(Url::class);
        $version                     = self::MAJOR_VERSION . '.' . self::MINOR_VERSION . '.' . self::BUILD;
        $this->default_configuration = [
            'wsdl_url'                       => $url->base() . '/ext/dhlsoap/geschaeftskundenversand-api-3.5.0.wsdl',
            'endpoint/sandbox'               => 'https://cig.dhl.de/services/sandbox/soap',
            'endpoint/live'                  => 'https://cig.dhl.de/services/production/soap',
            'mode'                           => 'live',// 'sandbox', 'live'
            'cig/live/user'                  => 'gambio2_2',
            'cig/live/password'              => 'wkF0ib6JVJABwYZM730JX3CAw8DkA9',
            'cig/sandbox/user'               => '',
            'cig/sandbox/password'           => '',
            'credentials/user'               => '',
            'credentials/password'           => '',
            'ekp'                            => '00000000000000',
            'returnreceiver/name1'           => '',
            'returnreceiver/name2'           => '',
            'returnreceiver/name3'           => '',
            'returnreceiver/streetname'      => '',
            'returnreceiver/streetnumber'    => '',
            'returnreceiver/addressaddition' => '',
            'returnreceiver/zip'             => '',
            'returnreceiver/city'            => '',
            'returnreceiver/origincountry'   => '',
            'returnreceiver/email'           => '',
            'returnreceiver/phone'           => '',
            'return_parcel_service_id'       => '0',
            'shipper/name1'                  => '',
            'shipper/name2'                  => '',
            'shipper/name3'                  => '',
            'shipper/streetname'             => '',
            'shipper/streetnumber'           => '',
            'shipper/addressaddition'        => '',
            'shipper/zip'                    => '',
            'shipper/city'                   => '',
            'shipper/origincountry'          => '',
            'shipper/email'                  => '',
            'shipper/phone'                  => '',
            'shipperreference'               => '',
            'bankdata/accountowner'          => '',
            'bankdata/bankname'              => '',
            'bankdata/iban'                  => '',
            'bankdata/note1'                 => '',
            'bankdata/note2'                 => '',
            'bankdata/bic'                   => '',
            'bankdata/accountreference'      => '%orders_id%',
            'order_status_after_label'       => '-1',
            'notify_customer'                => '0',
            'parcel_service_id'              => '0',
            'prefill_email'                  => '0',
            'prefill_phone'                  => '0',
            'open_in_new_tab'                => '1',
            'cod_add_fee'                    => '0',
            'create_return_label'            => '0',
            'return_shipment_account_number' => '',
            'age_check'                      => 'none',
            'intlpremium'                    => 'never',
            // never|eu-only|always
            'add_packing_weight'             => '0',
            'combined_printing'              => '0',
            'endorsement'                    => 'IMMEDIATE',
            'label_format'                   => 'GUI',
            'label_format_retoure'           => 'GUI',
            'returns/user'                   => '',
            'returns/password'               => '',
            'returns/email'                  => '',
            'returns/telephone'              => '',
            'returns/receiver_id/bel'        => 'bel',
            'returns/receiver_id/bgr'        => 'bgr',
            'returns/receiver_id/dnk'        => 'dnk',
            'returns/receiver_id/deu'        => 'deu',
            'returns/receiver_id/est'        => 'est',
            'returns/receiver_id/fin'        => 'fin',
            'returns/receiver_id/fra'        => 'fra',
            'returns/receiver_id/grc'        => 'grc',
            'returns/receiver_id/gbr'        => 'gbr',
            'returns/receiver_id/irl'        => 'irl',
            'returns/receiver_id/ita'        => 'ita',
            'returns/receiver_id/hrv'        => 'hrv',
            'returns/receiver_id/lva'        => 'lva',
            'returns/receiver_id/ltu'        => 'ltu',
            'returns/receiver_id/lux'        => 'lux',
            'returns/receiver_id/mlt'        => 'mlt',
            'returns/receiver_id/nld'        => 'nld',
            'returns/receiver_id/aut'        => 'aut',
            'returns/receiver_id/pol'        => 'pol',
            'returns/receiver_id/prt'        => 'prt',
            'returns/receiver_id/rou'        => 'rou',
            'returns/receiver_id/swe'        => 'swe',
            'returns/receiver_id/che'        => 'che',
            'returns/receiver_id/svk'        => 'svk',
            'returns/receiver_id/svn'        => 'svn',
            'returns/receiver_id/esp'        => 'esp',
            'returns/receiver_id/cze'        => 'cze',
            'returns/receiver_id/hun'        => 'hun',
            'returns/receiver_id/cyp'        => 'cyp',
            'checkout_preferences_modules'   => '',
            'returns/parcel_service_id'      => '0',
            'preferred_day_offset'           => '0',
            'use_preferred_neighbour'        => '1',
            'use_preferred_location'         => '1',
            'use_preferred_day'              => '1',
        ];
    
        /** @var Application $container */
        $container = LegacyDependencyContainer::getInstance();
        /** @var \Gambio\Core\Configuration\ConfigurationService $configurationService */
        $configurationService                                = $container->get(ConfigurationService::class);
        $this->default_configuration['shipper/name1']        = $configurationService->find('configuration/STORE_OWNER')
            ->value();
        $this->default_configuration['shipper/name2']        = $configurationService->find('configuration/COMPANY_NAME')
            ->value();
        $this->default_configuration['shipper/streetname']   = $configurationService->find('configuration/TRADER_STREET')
            ->value();
        $this->default_configuration['shipper/streetnumber'] = $configurationService->find('configuration/TRADER_STREET_NUMBER')
            ->value();
        $this->default_configuration['shipper/city']         = $configurationService->find('configuration/TRADER_LOCATION')
            ->value();
        $this->default_configuration['shipper/zip']          = $configurationService->find('configuration/TRADER_ZIPCODE')
            ->value();
        $shipperCountryId                                    = $configurationService->find('configuration/STORE_COUNTRY')
            ->value();
        /** @var CountryServiceInterface $countryService */
        $countryService                                       = StaticGXCoreLoader::getService('Country');
        $country                                              = $countryService->getCountryById(new IdType((int)$shipperCountryId));
        $this->default_configuration['shipper/origincountry'] = (string)$country->getIso2();
        $this->default_configuration['shipper/email']         = $configurationService->find('configuration/STORE_OWNER_EMAIL_ADDRESS')
            ->value();
        $this->default_configuration['shipper/phone']         = $configurationService->find('configuration/TRADER_TEL')
            ->value();
        
        $this->default_configuration['returnreceiver/name1']         = $this->default_configuration['shipper/name1'];
        $this->default_configuration['returnreceiver/name2']         = $this->default_configuration['shipper/name2'];
        $this->default_configuration['returnreceiver/streetname']    = $this->default_configuration['shipper/streetname'];
        $this->default_configuration['returnreceiver/streetnumber']  = $this->default_configuration['shipper/streetnumber'];
        $this->default_configuration['returnreceiver/city']          = $this->default_configuration['shipper/city'];
        $this->default_configuration['returnreceiver/zip']           = $this->default_configuration['shipper/zip'];
        $this->default_configuration['returnreceiver/origincountry'] = $this->default_configuration['shipper/origincountry'];
        $this->default_configuration['returnreceiver/email']         = $this->default_configuration['shipper/email'];
        $this->default_configuration['returnreceiver/phone']         = $this->default_configuration['shipper/phone'];
    
        $this->default_configuration['returns/email']     = $configurationService->find('configuration/STORE_OWNER_EMAIL_ADDRESS')
            ->value();
        $this->default_configuration['returns/telephone'] = $configurationService->find('configuration/TRADER_TEL')
            ->value();
    }
    
    
    /**
     * returns a single configuration value by its key
     *
     * @param string $key a configuration key (relative to the namespace prefix)
     *
     * @return string configuration value
     */
    public function get($key)
    {
        $connection = LegacyDependencyContainer::getInstance()->get(Connection::class);
        $reader     = new ConfigurationStorageReader($connection, $this->_namespace);
        $value      = $reader->get($key);
        
        if ($value === null && array_key_exists($key, $this->default_configuration)) {
            $value = $this->default_configuration[$key];
        }
        
        return $value;
    }
    
    
    /**
     * Retrieves all keys/values from a given prefix namespace
     *
     * @param string $p_prefix
     *
     * @return array
     */
    public function get_all($p_prefix = '')
    {
        $values = [];
        foreach ($this->default_configuration as $key => $default_value) {
            $key_prefix = substr($key, 0, strlen($p_prefix));
            if ($key_prefix == $p_prefix) {
                $values[$key] = $default_value;
            }
        }
        $values = array_merge($values, parent::get_all($p_prefix));
        
        return $values;
    }
    
    
    public function addProduct(GeschaeftskundenversandProduct $product)
    {
        $products     = $this->getProducts();
        $productIndex = empty($products) ? 0 : max(array_keys($products)) + 1;
        $keyPrefix    = sprintf('products/%d/', $productIndex);
        $this->set($keyPrefix . 'type', $product->getType());
        $this->set($keyPrefix . 'attendance', $product->getAttendance());
        $this->set($keyPrefix . 'alias', $product->getAlias());
    }
    
    
    public function getProducts()
    {
        $productsConfiguration = $this->get_all_tree('products');
        $products              = [];
        if (!empty($productsConfiguration)) {
            foreach ($productsConfiguration['products'] as $productsIndex => $productConfig) {
                $products[$productsIndex] = MainFactory::create('GeschaeftskundenversandProduct',
                                                                $productConfig['type'],
                                                                $productConfig['attendance'],
                                                                $productConfig['alias']);
            }
        }
        
        return $products;
    }
    
    
    /**
     * stores a configuration value by name/key
     *
     * @param string $name  name/key of configuration entry
     * @param string $value value to be stored
     *
     * @throws Exception if data validation fails
     */
    public function set($name, $value)
    {
        $checkName = preg_replace('_^products/\d+/(type|attendance|alias)$_', 'products/#/$1', $name);
        
        switch ($checkName) {
            case 'wsdl_url':
                $value = null;
                break;
            case 'mode':
                $value = in_array($value, ['live', 'sandbox']) ? $value : 'sandbox';
                break;
            case 'age_check':
                $value = in_array($value, ['visualage18', 'identcheck18', 'none'], true) ? $value : 'none';
                break;
            case 'ekp':
                $value = trim($value);
                if (preg_match('/^\d{10}$/', $value) !== 1) {
                    throw new InvalidEkpFormatException();
                }
                break;
            case 'return_shipment_account_number':
                $value = substr((string)$value, 0, 14);
                break;
            case 'cig/live/user':
            case 'cig/live/password':
            case 'cig/sandbox/user':
            case 'cig/sandbox/password':
            case 'credentials/password':
            case 'returnreceiver/name1':
            case 'returnreceiver/name2':
            case 'returnreceiver/name3':
            case 'returnreceiver/streetname':
            case 'returnreceiver/streetnumber':
            case 'returnreceiver/zip':
            case 'returnreceiver/city':
            case 'returnreceiver/origincountry':
            case 'returnreceiver/email':
            case 'returnreceiver/phone':
            case 'shipper/name1':
            case 'shipper/name2':
            case 'shipper/name3':
            case 'shipper/streetname':
            case 'shipper/streetnumber':
            case 'shipper/zip':
            case 'shipper/city':
            case 'shipper/origincountry':
            case 'shipper/email':
            case 'shipper/phone':
            case 'shipperreference':
            case 'bankdata/accountowner':
            case 'bankdata/bankname':
            case 'bankdata/note1':
            case 'bankdata/note2':
            case 'bankdata/bic':
            case 'bankdata/accountreference':
            case 'returns/user':
            case 'returns/password':
            case 'returns/email':
            case 'returns/telephone':
            case 'returns/receiver_id/bel':
            case 'returns/receiver_id/bgr':
            case 'returns/receiver_id/dnk':
            case 'returns/receiver_id/deu':
            case 'returns/receiver_id/est':
            case 'returns/receiver_id/fin':
            case 'returns/receiver_id/fra':
            case 'returns/receiver_id/grc':
            case 'returns/receiver_id/gbr':
            case 'returns/receiver_id/irl':
            case 'returns/receiver_id/ita':
            case 'returns/receiver_id/hrv':
            case 'returns/receiver_id/lva':
            case 'returns/receiver_id/ltu':
            case 'returns/receiver_id/lux':
            case 'returns/receiver_id/mlt':
            case 'returns/receiver_id/nld':
            case 'returns/receiver_id/aut':
            case 'returns/receiver_id/pol':
            case 'returns/receiver_id/prt':
            case 'returns/receiver_id/rou':
            case 'returns/receiver_id/swe':
            case 'returns/receiver_id/che':
            case 'returns/receiver_id/svk':
            case 'returns/receiver_id/svn':
            case 'returns/receiver_id/esp':
            case 'returns/receiver_id/cze':
            case 'returns/receiver_id/hun':
            case 'returns/receiver_id/cyp':
            case 'checkout_preferences_modules':
                $value = trim((string)$value);
                break;
            case 'bankdata/iban':
                $value = preg_replace('/\s/', '', (string)$value);
                break;
            case 'credentials/user':
                $value = strtolower(trim((string)$value));
                break;
            case 'products/#/type':
            case 'products/#/alias':
                $value = (string)$value;
                break;
            case 'products/#/attendance':
                $value = strtoupper(substr(trim($value), 0, 2));
                if (preg_match('/^[[:alnum:]]{2}$/', $value) !== 1) {
                    die($value);
                    $value = '00';
                }
                break;
            case 'order_status_after_label':
            case 'parcel_service_id':
            case 'return_parcel_service_id':
            case 'returns/parcel_service_id':
            case 'preferred_day_offset':
                $value = (int)$value;
                break;
            case 'notify_customer':
            case 'prefill_phone':
            case 'prefill_email':
            case 'open_in_new_tab':
            case 'cod_add_fee':
            case 'create_return_label':
            case 'add_packing_weight':
            case 'combined_printing':
            case 'use_preferred_neighbour':
            case 'use_preferred_location':
            case 'use_preferred_day':
                $value = (bool)$value ? '1' : '0';
                break;
            case 'intlpremium':
                $value = in_array($value, ['never', 'eu-only', 'always'], true) ? $value : 'never';
                break;
            case 'endorsement':
                $value = in_array($value, ['IMMEDIATE', 'ABANDONMENT']) ? $value : 'IMMEDIATE';
                break;
            case 'label_format':
            case 'label_format_retoure':
                $value = (string)$value;
                $value = in_array($value,
                                  [
                                      'GUI',
                                      'A4',
                                      '910-300-700',
                                      '910-300-700-oZ',
                                      '910-300-300',
                                      '910-300-300-oz',
                                      '910-300-710',
                                      '910-300-600',
                                      '910-300-400',
                                      '100x70mm',
                                  ],
                                  true) ? $value : 'GUI';
                break;
            default:
                //throw new Exception(sprintf('tried to set invalid key %s in %s', $key, __CLASS__));
                $value = null;
        }
        
        if ($value === null) {
            return;
        }
        parent::set($name, $value);
    }
    
    
    public function deleteProduct($index)
    {
        $prefix = sprintf('products/%d', (int)$index);
        $this->delete_all($prefix);
    }
    
}
