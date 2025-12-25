<?php
/* --------------------------------------------------------------
	InternetMarkeConfigurationStorage.inc.php 2019-08-01
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class InternetMarkeConfigurationStorage extends ConfigurationStorage
{
    const CONFIG_STORAGE_NAMESPACE = 'modules/shipping/internetmarke';
    protected $default_configuration;
    
    
    public function __construct()
    {
        parent::__construct(self::CONFIG_STORAGE_NAMESPACE);
        $this->setDefaultConfiguration();
    }
    
    
    protected function setDefaultConfiguration()
    {
        $countryService = StaticGXCoreLoader::getService('Country');
        $storeCountry   = $countryService->getCountryById(new IdType(STORE_COUNTRY));
        
        $this->default_configuration = [
            'prodws/endpoint_live'                     => 'https://prodws.deutschepost.de:8443/ProdWSProvider_1_1/prodws?wsdl',
            'prodws/credentials/id'                    => 'GAMBIO',
            'prodws/credentials/user'                  => 'gambio',
            'prodws/credentials/password'              => 'aVxb&py7Cn',
            'oneclick4app/endpoint_live'               => 'https://internetmarke.deutschepost.de/OneClickForAppV3?wsdl',
            'oneclick4app/key'                         => 'xtN6Z5Si158tKTd7TFJa0QO5Yf80U03R',
            'oneclick4app/key_phase'                   => '1',
            'oneclick4app/credentials/email'           => '',
            'oneclick4app/credentials/password'        => '',
            'oneclick4app/tos_accepted'                => '0',
            'oneclick4app/sender/company'              => COMPANY_NAME,
            'oneclick4app/sender/firstname'            => TRADER_FIRSTNAME,
            'oneclick4app/sender/lastname'             => TRADER_NAME,
            'oneclick4app/sender/street'               => TRADER_STREET,
            'oneclick4app/sender/houseno'              => TRADER_STREET_NUMBER,
            'oneclick4app/sender/zip'                  => TRADER_ZIPCODE,
            'oneclick4app/sender/city'                 => TRADER_LOCATION,
            'oneclick4app/sender/country'              => (string)$storeCountry->getIso3(),
            'oneclick4app/prefs/productcode'           => '1',
            'oneclick4app/prefs/voucherlayout'         => 'AddressZone',
            'oneclick4app/prefs/pageformatid'          => '1',
            'oneclick4app/prefs/imageid'               => '-1',
            'oneclick4app/parcelservice_id'            => '0',
            'oneclick4app/order_status_after_label'    => '-1',
            'oneclick4app/notify_customer'             => '1',
            'oneclick4app/low_wallet_balance'          => '1',
            'oneclick4app/show_contract_products_only' => '1',
            'oneclick4app/favorite_products'           => '',
        ];
    }
    
    
    public function set($name, $value)
    {
        switch ($name) {
            case 'prodws/endpoint_live':
            case 'prodws/endpoint_test':
            case 'prodws/credentials/id':
            case 'prodws/credentials/user':
            case 'prodws/credentials/password':
            case 'oneclick4app/endpoint_live':
            case 'oneclick4app/key':
            case 'oneclick4app/key_phase':
            case 'oneclick4app/credentials/email':
            case 'oneclick4app/credentials/password':
            case 'oneclick4app/sender/company':
            case 'oneclick4app/sender/firstname':
            case 'oneclick4app/sender/lastname':
            case 'oneclick4app/sender/street':
            case 'oneclick4app/sender/houseno':
            case 'oneclick4app/sender/zip':
            case 'oneclick4app/sender/city':
            case 'oneclick4app/sender/country':
                $value = trim((string)$value);
                $value = strip_tags($value);
                break;
            case 'oneclick4app/prefs/productcode':
            case 'oneclick4app/prefs/pageformatid':
            case 'oneclick4app/prefs/imageid':
            case 'oneclick4app/parcelservice_id':
            case 'oneclick4app/order_status_after_label':
            case 'oneclick4app/notify_customer':
            case 'oneclick4app/tos_accepted':
            case 'oneclick4app/low_wallet_balance':
            case 'oneclick4app/show_contract_products_only':
                $value = (int)$value;
                break;
            case 'oneclick4app/prefs/voucherlayout':
                $value = trim((string)$value);
                if (!in_array($value, ['FrankingZone', 'AddressZone'])) {
                    throw new Exception(sprintf('%s: Invalid value %s for key %s', __CLASS__, $value, $name));
                }
                break;
            case 'oneclick4app/favorite_products':
                if (is_string($value)) {
                    $value = trim((string)$value);
                    if (preg_match('/^(\d+)?(,\d+)*$/', $value) !== 1) {
                        throw new Exception(sprintf('%s: Invalid value %s for key %s', __CLASS__, $value, $name));
                    }
                }
                if (is_array($value)) {
                    $value = array_map(static function($el) { return (int)$el; }, $value);
                    $value = implode(',', $value);
                }
                break;
            default:
                throw new Exception(sprintf('tried to set invalid key %s in %s', $name, __CLASS__));
        }
        parent::set($name, $value);
    }
    
    
    public function setForSession($key, $value)
    {
        if (!is_array($_SESSION['oneclick4app_session_config'])) {
            $_SESSION['oneclick4app_session_config'] = [];
        }
        $_SESSION['oneclick4app_session_config'][$key] = $value;
    }
    
    
    public function credentialsRequired()
    {
        $email               = $this->get('oneclick4app/credentials/email');
        $password            = $this->get('oneclick4app/credentials/password');
        $credentialsRequired = empty($email) || empty($password);
        
        return $credentialsRequired;
    }
    
    
    public function get($key)
    {
        $value = $this->getStored($key);
        
        // 'oneclick4app/credentials/email', 'oneclick4app/credentials/password'
        if (!empty($_SESSION['oneclick4app_session_config'])
            && array_key_exists($key,
                                $_SESSION['oneclick4app_session_config'])) {
            $value = $_SESSION['oneclick4app_session_config'][$key];
        }
        
        if ($key === 'oneclick4app/favorite_products') {
            $value = explode(',', $value);
        }
        
        return $value;
    }
    
    
    public function getStored($key)
    {
        $value = parent::get($key);
        if ($value === false && array_key_exists($key, $this->default_configuration)) {
            $value = $this->default_configuration[$key];
        }
        
        return $value;
    }
}
