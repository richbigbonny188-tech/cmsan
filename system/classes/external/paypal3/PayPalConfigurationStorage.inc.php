<?php
/* --------------------------------------------------------------
	PayPalConfigurationStorage.inc.php 2021-04-19
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2018 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * subclass of ConfigurationStorage for parameters concerning PayPal3
 */
class PayPalConfigurationStorage extends ConfigurationStorage
{
	/**
	 * namespace inside the configuration storage
	 */
	const CONFIG_STORAGE_NAMESPACE = 'modules/payment/paypal3';

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
		$this->default_configuration = [
			'mode'                                            => 'live',
			'service_base_url/sandbox'                        => 'https://api.sandbox.paypal.com',
			'service_base_url/live'                           => 'https://api.paypal.com',
			'restapi-credentials/sandbox/client_id'           => '',
			'restapi-credentials/sandbox/secret'              => '',
			'restapi-credentials/live/client_id'              => '',
			'restapi-credentials/live/secret'                 => '',
			'payment_experience_profile_id'                   => '',
			'use_paypal_plus'                                 => '0',
			'use_ecs_cart'                                    => '0',
			'use_ecs_products'                                => '0',
			'allow_ecs_login'                                 => '0',
			'webhook_id'                                      => '',
			'intent'                                          => 'sale',
			'intent_installments'                             => 'sale',
			'ecs_button_style'                                => 'Silver',  // Sunrise|Silver
			'thirdparty_payments/invoice/mode'                => 'external', // paywall|external
			'thirdparty_payments/cod/mode'                    => 'external',
			'thirdparty_payments/moneyorder/mode'             => 'external',
			'thirdparty_payments/eustandardtransfer/mode'     => 'external',
			'thirdparty_payments/cash/mode'                   => 'external',
			'orderstatus/completed'                           => @constant('DEFAULT_ORDERS_STATUS_ID'),
			'orderstatus/pending'                             => @constant('DEFAULT_ORDERS_STATUS_ID'),
			'orderstatus/error'                               => @constant('DEFAULT_ORDERS_STATUS_ID'),
			'debug_logging'                                   => '1',
			'allow_selfpickup'                                => '0',
			'show_installments_presentment_specific_product'  => '0',
			'show_installments_presentment_specific_cart'     => '0',
			'show_installments_presentment_specific_payment'  => '0',
			'show_installments_presentment_specific_computed' => '0',
		];

		$Language = new language();
		foreach($Language->catalog_languages as $iso2 => $langData)
		{
			$this->default_configuration['payment_experience_profile/'.$langData['code']] = '';
			$this->default_configuration['payment_experience_profile_sandbox/'.$langData['code']] = '';
		}
	}

	/**
	 * returns a single configuration value by its key
	 * @param string $key a configuration key (relative to the namespace prefix)
	 * @return string configuration value
	 */
	public function get($key)
	{
	    if (strpos($key, 'thirdparty_payments') !== false) {
	        return 'external';
        }
		$value = parent::get($key);
		if($value === false && array_key_exists($key, $this->default_configuration))
		{
			$value = $this->default_configuration[$key];
		}
		return $value;
	}

	/**
	 * stores a configuration value by name/key
	 * @param string $name name/key of configuration entry
	 * @param string $value value to be stored
	 * @throws Exception if data validation fails
	 */
	public function set($name, $value)
	{
		if($value === null)
		{
			return;
		}

		switch($name)
		{
			case 'mode':
				if(!in_array($value, array('sandbox', 'live')))
				{
					throw new Exception(__CLASS__.': invalid value '.$value.' for '.$name);
				}
				break;
			case 'intent':
				if(!in_array($value, array('sale', 'authorize', 'order')))
				{
					throw new Exception(__CLASS__.': invalid value '.$value.' for '.$name);
				}
				break;
			case 'intent_installments':
				if(!in_array($value, array('sale', 'order')))
				{
					throw new Exception(__CLASS__.': invalid value '.$value.' for '.$name);
				}
				break;
			case 'ecs_button_style':
				if(!in_array($value, array('Silver', 'Sunrise')))
				{
					throw new Exception(__CLASS__.': invalid value '.$value.' for '.$name);
				}
				break;
			case 'use_paypal_plus':
			case 'use_ecs_cart':
			case 'use_ecs_products':
			case 'allow_ecs_login':
			case 'show_installments_presentment_specific_product':
			case 'show_installments_presentment_specific_cart':
			case 'show_installments_presentment_specific_payment':
			case 'show_installments_presentment_specific_computed':
				$value = ($value == true ? '1' : '0');
				break;
			case 'thirdparty_payments/invoice/mode':
			case 'thirdparty_payments/cod/mode':
			case 'thirdparty_payments/moneyorder/mode':
			case 'thirdparty_payments/eustandardtransfer/mode':
			case 'thirdparty_payments/cash/mode':
				if(!in_array($value, array('paywall', 'external')))
				{
					throw new Exception(__CLASS__.': invalid value '.$value.' for '.$name);
				}
				break;
			case 'orderstatus/completed':
			case 'orderstatus/pending':
			case 'orderstatus/error':
				$value = (string)(int)$value;
				break;
			case 'debug_logging':
				$value = ($value == true ? '1' : '0');
				break;
			case 'require_instant_funding':
				$value = in_array($value, ['nonphysical', 'b2b', 'always'], true) ? $value : 'nonphysical';
				break;
			default:
		}
		parent::set($name, $value);
	}

}
