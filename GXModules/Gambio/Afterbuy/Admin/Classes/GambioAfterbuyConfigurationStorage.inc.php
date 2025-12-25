<?php
/* --------------------------------------------------------------
   GambioAfterbuyConfigurationStorage.inc.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class GambioAfterbuyConfigurationStorage
 *
 * @package GXModules\Gambio\Afterbuy\Admin\Classes
 */
class GambioAfterbuyConfigurationStorage extends ConfigurationStorage
{
    /**
     * namespace inside the configuration storage
     */
    protected const CONFIG_STORAGE_NAMESPACE = 'modules/gambio/afterbuy';
    
    /**
     * @var array holding default values to be used in absence of configured values
     */
    protected array $default_configuration;
    
    
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
     *
     * @return void
     */
    protected function setDefaultConfiguration(): void
    {
        $this->default_configuration = [
            'minimum_log_level'                => 'info',
            'active'                           => '0',
            'partner_id'                       => defined('AFTERBUY_PARTNERID') ? (string)@constant('AFTERBUY_PARTNERID') : '',
            'partner_password'                 => defined('AFTERBUY_PARTNERPASS') ? (string)@constant('AFTERBUY_PARTNERPASS') : '',
            'user_id'                          => defined('AFTERBUY_USERID') ? (string)@constant('AFTERBUY_USERID') : '',
            'order_status'                     => defined('AFTERBUY_ORDERSTATUS') ? (string)@constant('AFTERBUY_ORDERSTATUS') : '',
            'order_status_paid'                => '-1',
            'order_status_not_paid'            => '',
            'order_status_tracking_sync'       => '-1',
            'order_status_shipping_date'       => '-1',
            'partner_token'                    => '',
            'account_token'                    => '',
            'last_qty_sync'                    => (new \DateTimeImmutable('30 days ago'))->format('c'),
            'product_sync_type'                => 'pid_ean',
            'import_catalogs_parent_category'  => 0,
            'import_products_default_category' => 0,
            'import_products_dealer_group'     => 0,
            'import_products_level_threshold'  => 1,
            'import_since_timestamp'           => 0,
            'import_last_product_id'           => 0,
            'import_last_page_number'          => 0,
            'import_max_products_per_run'      => 10,
            'import_tax_rate_1'                => '19',
            'import_tax_class_id_1'            => '1',
            'import_tax_rate_2'                => '7',
            'import_tax_class_id_2'            => '2',
            'import_tax_rate_3'                => '0',
            'import_tax_class_id_3'            => '0',
            'import_tax_rate_4'                => '0',
            'import_tax_class_id_4'            => '0',
            'import_tax_rate_5'                => '0',
            'import_tax_class_id_5'            => '0',
            'import_mode'                      => 'stocksync',
            'last_tracking_sync'               => (new \DateTimeImmutable('30 days ago'))->format('c'),
            'tracking_sync_parcel_service_id'  => '',
            'tracking_sync_shipping_methods'   => '',
            'use_separate_tax'                 => 1,
            'use_correctional_items'           => 1,
            'send_shipping_info'               => 'always',
        ];
    }
    
    
    /**
     * returns a single configuration value by its key
     *
     * @param string $key a configuration key (relative to the namespace prefix)
     *
     * @return string configuration value
     */
    public function get($key): string
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
    public function get_all($p_prefix = ''): array
    {
        $values = parent::get_all($p_prefix);
        foreach ($this->default_configuration as $key => $default_value) {
            $key_prefix = substr($key, 0, strlen($p_prefix));
            if (!array_key_exists($key, $values) && $key_prefix === $p_prefix) {
                $values[$key] = $default_value;
            }
            if ($key === 'order_status_paid' || $key === 'order_status_not_paid') {
                $value        = explode(',', $values[$key]);
                $values[$key] = $value;
            }
        }
        
        return $values;
    }
    
    
    /**
     * @param $p_key
     * @param $p_value
     *
     * @return void|null
     * @throws Exception
     */
    public function set($p_key, $p_value)
    {
        switch ($p_key) {
            case 'active':
            case 'use_separate_tax':
            case 'use_correctional_items':
                $value = (bool)$p_value ? '1' : '0';
                break;
            case 'partner_id':
                $value = (string)(int)$p_value;
                break;
            case 'partner_password':
            case 'user_id':
            case 'partner_token':
            case 'minimum_log_level':
            case 'account_token':
            case 'tracking_sync_shipping_methods':
                $value = strip_tags($p_value);
                break;
            case 'order_status':
            case 'order_status_paid':
            case 'order_status_not_paid':
            case 'order_status_tracking_sync':
            case 'order_status_shipping_date':
                if (is_array($p_value)) {
                    if (($key = array_search('-1', $p_value, true)) !== false && count($p_value) > 1) {
                        unset($p_value[$key]);
                    }
                    $value = implode(',', $p_value);
                } else {
                    $value = $p_value;
                }
                $value = strip_tags($value);
                break;
            case 'import_catalogs_parent_category':
            case 'import_products_dealer_group':
            case 'import_products_default_category':
            case 'import_products_level_threshold':
            case 'import_since_timestamp':
            case 'import_last_product_id':
            case 'import_last_page_number':
            case 'import_max_products_per_run':
            case 'import_tax_class_id_1':
            case 'import_tax_class_id_2':
            case 'import_tax_class_id_3':
            case 'import_tax_class_id_4':
            case 'import_tax_class_id_5':
            case 'tracking_sync_parcel_service_id':
                $value = (int)$p_value;
                break;
            case 'last_qty_sync':
            case 'last_tracking_sync':
                $value = (new \DateTimeImmutable((string)$p_value))->format('c');
                break;
            case 'product_sync_type':
                $allowedValues = [
                    "pid_ean",
                    "pid_anr",
                    "model_ean",
                    "model_anr",
                    "model_prodid",
                ];
                $value         = in_array((string)$p_value, $allowedValues) ? (string)$p_value : 'pid_ean';
                break;
            case 'import_tax_rate_1':
            case 'import_tax_rate_2':
            case 'import_tax_rate_3':
            case 'import_tax_rate_4':
            case 'import_tax_rate_5':
                $value = (string)(float)$p_value;
                break;
            case 'import_mode':
                $value = in_array($p_value, ['stocksync', 'products_import']) ? $p_value : 'stocksync';
                break;
            case 'send_shipping_info':
                $value = in_array($p_value, ['never', 'initially', 'always']) ? $p_value : 'always';
                break;
            default:
                $value = null;
        }
        if ($value === null && $this->strStartWith($p_key, 'tracking_sync_shipping_methods_')) {
            if ($p_value === null) {
                $p_value = '';
            }
            $value = strip_tags($p_value);
        }
        
        if ($value !== null) {
            $rc = parent::set($p_key, $value);
        }
        
        return $rc ?? null;
    }
    
    
    /**
     * Copied from symfony polyfill. Checks if $haystack starts with $needle.
     *
     * @param string $haystack
     * @param string $needle
     *
     * @return bool
     */
    protected function strStartWith(string $haystack, string $needle): bool
    {
        return 0 === strncmp($haystack, $needle, strlen($needle));
    }
}
