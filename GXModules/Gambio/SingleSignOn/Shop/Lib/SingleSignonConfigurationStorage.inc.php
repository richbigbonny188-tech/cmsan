<?php
/* --------------------------------------------------------------
   SingleSignonConfigurationStorage.inc.php 2017-09-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class SingleSignonConfigurationStorage extends ConfigurationStorage
{
    /**
     * namespace inside the configuration storage
     */
    const CONFIG_STORAGE_NAMESPACE = 'modules/gambio/singlesignon';
    
    /**
     * array holding default values to be used in absence of configured values
     */
    protected $default_configuration;
    
    
    /**
     * constructor; initializes default configuration
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
        $httpServer   = defined('HTTP_SERVER') ? constant('HTTP_SERVER') : constant('HTTP_CATALOG_SERVER');
        $httpsServer  = defined('HTTPS_SERVER') ? constant('HTTPS_SERVER') : constant('HTTPS_CATALOG_SERVER');
        $isSslEnabled = defined('ENABLE_SSL') ? ENABLE_SSL : (ENABLE_SSL_CATALOG === 'true');
        $baseUrl      = get_href_link($httpServer,
                                      $httpsServer,
                                      DIR_WS_CATALOG,
                                      $isSslEnabled,
                                      'shop.php',
                                      '',
                                      'SSL',
                                      false,
                                      false,
                                      false,
                                      false,
                                      true);
        
        $paypalConfiguration       = MainFactory::create('PayPalConfigurationStorage');
        $paypalClientId            = $paypalConfiguration->get('restapi-credentials/live/client_id');
        $paypalClientSecret        = $paypalConfiguration->get('restapi-credentials/live/secret');
        $paypalClientIdSandbox     = $paypalConfiguration->get('restapi-credentials/sandbox/client_id');
        $paypalClientSecretSandbox = $paypalConfiguration->get('restapi-credentials/sandbox/secret');
        
        $this->default_configuration = [
            'services/google/active'              => '0',
            'services/google/clientId'            => '',
            'services/google/clientSecret'        => '',
            'services/google/redirectUri'         => $baseUrl . '?do=SingleSignOn/Login&service=google',
            'services/facebook/active'            => '0',
            'services/facebook/clientId'          => '',
            'services/facebook/clientSecret'      => '',
            'services/facebook/redirectUri'       => $baseUrl . '?do=SingleSignOn%2FLogin&service=facebook',
            'services/paypal/active'              => '0',
            'services/paypal/mode'                => 'sandbox',
            'services/paypal/clientId'            => $paypalClientId,
            'services/paypal/clientSecret'        => $paypalClientSecret,
            'services/paypal/clientIdSandbox'     => $paypalClientIdSandbox,
            'services/paypal/clientSecretSandbox' => $paypalClientSecretSandbox,
            'services/paypal/redirectUri'         => $baseUrl . '?do=SingleSignOn/Login&service=paypal',
            'services/amazon/active'              => '0',
            'services/amazon/clientId'            => '',
            'services/amazon/clientSecret'        => '',
            'services/amazon/redirectUri'         => $baseUrl . '?do=SingleSignOn/Login&service=amazon',
            'services/amazon/javascriptOrigin'    => $httpsServer,
            'services/amazon/mode'                => 'sandbox',
        ];
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
        $value = parent::get($key);
        if ($value === false && array_key_exists($key, $this->default_configuration)) {
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
        $values = parent::get_all($p_prefix);
        foreach ($this->default_configuration as $key => $default_value) {
            $key_prefix = substr($key, 0, strlen($p_prefix));
            if (!array_key_exists($key, $values) && $key_prefix === $p_prefix) {
                $values[$key] = $default_value;
            }
        }
        
        return $values;
    }
    
    
    public function set($p_key, $p_value)
    {
        switch ($p_key) {
            case 'services/google/clientId':
            case 'services/google/clientSecret':
            case 'services/facebook/clientId':
            case 'services/facebook/clientSecret':
            case 'services/paypal/clientId':
            case 'services/paypal/clientSecret':
            case 'services/paypal/clientIdSandbox':
            case 'services/paypal/clientSecretSandbox':
            case 'services/amazon/clientId':
            case 'services/amazon/clientSecret':
                $value = trim(strip_tags($p_value));
                break;
            case 'services/paypal/mode':
            case 'services/amazon/mode':
                if (in_array($p_value, ['1', 'sandbox'], true)) {
                    $value = 'sandbox';
                } else {
                    $value = 'live';
                }
                break;
            case 'services/google/active':
            case 'services/facebook/active':
            case 'services/paypal/active':
            case 'services/amazon/active':
                $value = (bool)$p_value ? '1' : '0';
                break;
            default:
                $value = null;
        }
        $rc = parent::set($p_key, $value);
        
        return $rc;
    }
}
