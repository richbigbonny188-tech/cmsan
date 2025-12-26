<?php
/* --------------------------------------------------------------
   gambio_hub.php 2023-05-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

$languageTextManager = MainFactory::create_object('LanguageTextManager', [], true);
$languageTextManager->init_from_lang_file('gambio_hub', $_SESSION['languages_id']);

use \HubPublic\Exceptions\CurlRequestException;
use \HubPublic\Exceptions\HubException;
use \HubPublic\Http\CurlRequest;
use \HubPublic\Serializers\CartContentSerializer;
use \HubPublic\Serializers\ClientSessionInformationSerializer;
use \HubPublic\Serializers\CustomerInformationSerializer;
use \HubPublic\Serializers\HubClientInformationSerializer;
use \HubPublic\Serializers\OrderContentSerializer;
use \HubPublic\ValueObjects\Builder\CustomerInformation as CustomerInformationBuilder;
use \HubPublic\ValueObjects\Builder\OrderContent as OrderContentBuilder;
use \HubPublic\ValueObjects\ClientSessionInformation;
use \HubPublic\ValueObjects\HubClientInformation;
use \HubPublic\ValueObjects\HubClientKey;
use \HubPublic\ValueObjects\HubSessionKey;
use \HubPublic\ValueObjects\HubTransactionCode;

/**
 * Class gambio_hub_ORIGIN.
 *
 * This module servers the Gambio Hub payment method in the shop. It will store in the database
 * the selected payment module and the transaction code for future reference. Make sure that you set
 * the correct Gambio Hub API URL in the settings.
 */
class gambio_hub_ORIGIN
{
	/**
	 * @var string
	 */
	public $code;

	/**
	 * @var string
	 */
	public $title;

	/**
	 * @var string
	 */
	public $description;
    
    /**
     * @var string
     */
    public $info;
    
    /**
     * @var string|int
     */
    public $order_status;

	/**
	 * @var bool
	 */
	public $enabled;

	/**
	 * @var bool
	 */
	public $tmpOrders = true;

    /**
     * @var array
     */
	protected $confirmationContents = [];

	/**
	 * Class Constructor
	 */
	public function __construct()
	{
		global $order;

		$this->code        = 'gambio_hub';
		$this->title       = defined('MODULE_PAYMENT_GAMBIO_HUB_TEXT_TITLE') ? MODULE_PAYMENT_GAMBIO_HUB_TEXT_TITLE : '';
		$this->description = defined('MODULE_PAYMENT_GAMBIO_HUB_TEXT_DESCRIPTION') ? MODULE_PAYMENT_GAMBIO_HUB_TEXT_DESCRIPTION : '';
        $this->enabled     = filter_var(@constant('MODULE_PAYMENT_' . strtoupper($this->code) . '_STATUS'),
                                        FILTER_VALIDATE_BOOLEAN);
		$this->info        = defined('MODULE_PAYMENT_GAMBIO_HUB_TEXT_INFO') ? MODULE_PAYMENT_GAMBIO_HUB_TEXT_INFO : '';

		if(defined('MODULE_PAYMENT_GAMBIO_HUB_ORDER_STATUS_ID') && (int)MODULE_PAYMENT_GAMBIO_HUB_ORDER_STATUS_ID > 0)
		{
			$this->order_status = MODULE_PAYMENT_GAMBIO_HUB_ORDER_STATUS_ID;
		}

		if(is_object($order))
		{
			$this->update_status();
		}

		// set gambio_hub_selection-SESSION-parameter in constructor, because ::pre_confirmation_check is too late for
		// ot-modules functionality
		if(isset($_POST) && array_key_exists('gambio_hub_selection', $_POST))
		{
			$_SESSION['gambio_hub_selection'] = $_POST['gambio_hub_selection'];
		}
        if(isset($_POST) && array_key_exists('gambio_hub_subselection', $_POST))
        {
            $_SESSION['gambio_hub_subselection'] = $_POST['gambio_hub_subselection'];
        }
        
        if (($_SESSION['payment'] ?? '') === 'gambio_hub'
            && strpos(gm_get_env_info('SCRIPT_NAME'), 'checkout_process.php') !== false
            && empty($_SESSION['gambio_hub_selection'])) {
            $this->writeLog('gambio_hub->__construct() misses "gambio_hub_selection" in the session.');
            
            $cacheControl = MainFactory::create_object('CacheControl');
            $cacheControl->clear_data_cache();
            
            // Redirect to checkout payment page, if Gambio Hub data is missing
            $_SESSION['gambio_hub_error'] = MODULE_PAYMENT_GAMBIO_HUB_ERROR;
            xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'payment_error=' . $this->code, 'SSL'));
        }
	}


	/**
	 * This method is not used.
	 *
	 * @return bool
	 */
	public function update_status()
	{
		return false;
	}


	/**
	 * This method is not used.
	 *
	 * @return bool
	 */
	public function javascript_validation()
	{
		return false;
	}


	/**
	 * Provides module selection information.
	 *
	 * @return array
	 */
	public function selection()
	{
		return [
			'id'          => $this->code,
			'module'      => $this->title,
			'description' => $this->info,
		];
	}


	/**
	 * Sets the "gambio_hub_selection" in the PHP Session and redirects to module page via outputted html if payment
	 * module needs a extra steps before the confirmation page like PayPal.
	 *
	 * @todo implement whitelisting for GET and POST data
	 *
	 * @return bool
	 */
	public function pre_confirmation_check()
	{
		$helper = MainFactory::create('HubCheckoutHelper');

		// Hub transactions API client.
		$hubTransactionsApiClient = $helper->createHubTransactionsApiClient(new HubSessionKey($_SESSION['gambio_hub_session_key'] ?? ''));

		$cartContent              = $helper->getCartContent($GLOBALS['order']);
		$customerInformation      = $helper->getCustomerInformation($GLOBALS['order']);
		$hubClientInformation     = $helper->getHubClientInformation();
		$clientSessionInformation = $helper->getClientSessionInformation($GLOBALS['order']);
		$getData                  = $_GET ? : [];
		$postData                 = $_POST ? : [];

		try
		{
			$pageContentHtml = $hubTransactionsApiClient->getBeforeTransactionPageContent($cartContent,
			                                                                              $customerInformation,
			                                                                              $hubClientInformation,
			                                                                              $clientSessionInformation,
			                                                                              $getData, $postData,
			                                                                              $_SESSION['gambio_hub_selection'] ?? null);

			if($pageContentHtml !== '')
			{
				echo $pageContentHtml;

				exit;
			}
		}
		catch(UnexpectedValueException $e)
		{
            $this->writeLog("gambio_hub->pre_confirmation_check() failed with UnexpectedValueException '{$e->getMessage()}'.");
			xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
		}
		catch(CurlRequestException $e)
		{
            $this->writeLog("gambio_hub->pre_confirmation_check() failed with CurlRequestException '{$e->getMessage()}'.");
			xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
		}

        if (($_SESSION['gambio_hub_selection'] ?? null) === 'PayPal2InstallmentsHub') {
            try {
                $confirmation = $this->getHubConfirmationContents();
                foreach($confirmation['additionalModuleValues'] as $key => $value)
                {
                    if ($key === 'paypalinstallmentshub_total_cost') {
                        $_SESSION['paypal_payment_installments']['total_cost'] = $value;
                    }
                    if ($key === 'paypalinstallmentshub_total_interest') {
                        $_SESSION['paypal_payment_installments']['total_interest'] = $value;
                    }
                }
            } catch (UnexpectedValueException $e) {
                $this->writeLog("gambio_hub->getHubConfirmationContents() failed with UnexpectedValueException '{$e->getMessage()}'.");
                xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
            } catch (CurlRequestException $e) {
                $this->writeLog("gambio_hub->getHubConfirmationContents() failed with CurlRequestException '{$e->getMessage()}'.");
                xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
            }
        }

        if (($_SESSION['gambio_hub_selection'] ?? null) === 'EasyCreditHub') {
            try {
                $confirmation = $this->getHubConfirmationContents();
            } catch (UnexpectedValueException $e) {
                $this->writeLog("gambio_hub->getHubConfirmationContents() failed with UnexpectedValueException '{$e->getMessage()}'.");
                xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
            } catch (CurlRequestException $e) {
                $this->writeLog("gambio_hub->getHubConfirmationContents() failed with CurlRequestException '{$e->getMessage()}'.");
                xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
            }
        }
        
		return false;
	}


	/**
	 * Returns the module confirmation info.
	 *
	 * @return array
	 */
	public function confirmation()
	{
		try
		{
		    $confirmation = $this->getHubConfirmationContents();

			$infoText                    = $_SESSION['gambio_hub_payments'][$_SESSION['gambio_hub_selection'] ?? '']['info_text'] ?? '';
			$confirmationArray           = ['title' => '', 'fields' => []];

			if($infoText !== '')
			{
				$confirmationArray['title'] = $infoText . '<br/><br/>';
			}

			$confirmationArray['title'] .= $confirmation['orderPaymentInstructions'];

			foreach($confirmation['additionalModuleValues'] as $key => $value)
			{
				$confirmationArray['fields'][] = ['title' => $key, 'field' => $value];
			}

			return $confirmationArray;
		}
		catch(UnexpectedValueException $e)
		{
            $this->writeLog("gambio_hub->confirmation() failed with UnexpectedValueException '{$e->getMessage()}'.");
			xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
		}
		catch(CurlRequestException $e)
		{
            $this->writeLog("gambio_hub->confirmation() failed with CurlRequestException '{$e->getMessage()}'.");
			xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
		}
	}


    /**
     * @return array|string
     * @throws \HubPublic\Exceptions\InvalidHubSessionKeyException
     * @throws \UnexpectedValueException
     * @throws \HubPublic\Exceptions\CurlRequestException
     */
    protected function getHubConfirmationContents()
    {
        if (empty($this->confirmationContents)) {
            $helper = MainFactory::create('HubCheckoutHelper');
            $hubTransactionsApiClient = $helper->createHubTransactionsApiClient(new HubSessionKey($_SESSION['gambio_hub_session_key'] ?? ''));
            $cartContent              = $helper->getCartContent($GLOBALS['order']);
            $customerInformation      = $helper->getCustomerInformation($GLOBALS['order']);
            $hubClientInformation     = $helper->getHubClientInformation();
            $clientSessionInformation = $helper->getClientSessionInformation($GLOBALS['order']);
            $getData                  = $_GET ? : [];
            $postData                 = $_POST ? : [];
            $this->confirmationContents = $hubTransactionsApiClient->getConfirmationContents(
                $cartContent,
                $customerInformation,
                $hubClientInformation,
                $clientSessionInformation,
                $getData,
                $postData,
                $_SESSION['gambio_hub_selection'] ?? null
            );
            if (strpos($this->confirmationContents['orderPaymentInstructions'], 'total interest') !== false) {
                $matches = [];
                if (preg_match('/total interest ([0-9.]+)\s+([[:alpha:]]+)/', $this->confirmationContents['orderPaymentInstructions'], $matches) === 1) {
                    $interestAmount = $matches[1];
                    $interestCurrency = $matches[2];
                    $_SESSION['checkout_interest'][$_SESSION['gambio_hub_session_key'] ?? ''] = [
                        'amount' => $interestAmount,
                        'currency' => $interestCurrency,
                    ];
                }
            }
        }

        return $this->confirmationContents;
    }

	/**
	 * This method is not used.
	 *
	 * @return bool
	 */
	public function process_button()
	{
		return false;
	}


	/**
	 * This method is not used.
	 *
	 * @return bool
	 */
	public function before_process()
	{
		return false;
	}


	/**
	 * Unsets hub session key after transaction completes.
	 *
	 * @return bool
	 */
	public function after_process()
	{
		if(array_key_exists('gambio_hub_session_key', $_SESSION))
		{
			unset($_SESSION['gambio_hub_session_key']);
		}

		return false;
	}
    
    
    /**
     * $_GET['messageSignature'] and $_GET['adminMessageSignature'] are deprecated and replaced by
     * $_GET['message_signature'] and $_GET['admin_message_signature']
     *
     * @return bool
     */
    public function get_error()
    {
        $error = false;
        if (isset($_SESSION['gambio_hub_error'])) {
            $error                        = [
                'error' => $_SESSION['gambio_hub_error'],
            ];
            $_SESSION['gambio_hub_error'] = null;
        } elseif (!empty($_GET['message'])
                  && (!empty($_GET['messageSignature'])
                      || !empty($_GET['message_signature']))) {
            $message          = $_GET['message'];
            $signature        = isset($_GET['message_signature']) ? $_GET['message_signature'] : $_GET['messageSignature'];
            $messageSignature = md5($message
                                    . (isset($_GET['message_signature']) ? gm_get_conf('GAMBIO_HUB_CLIENT_KEY') : ($_SESSION['gambio_hub_session_key'] ?? '')));
            if ($messageSignature === $signature) {
                $error = [
                    'error' => strip_tags($message),
                ];
            }
            
            if (isset($_GET['adminMessage'], $_GET['orderNumber'])
                && (isset($_GET['adminMessageSignature'])
                    || isset($_GET['admin_message_signature']))) {
                $adminMessage          = base64_decode($_GET['adminMessage']);
                $orderNumber           = (int)$_GET['orderNumber'];
                $adminSignature        = isset($_GET['admin_message_signature']) ? $_GET['admin_message_signature'] : $_GET['adminMessageSignature'];
                $adminMessageSignature = md5($orderNumber . $adminMessage
                                             . (isset($_GET['admin_message_signature']) ? gm_get_conf('GAMBIO_HUB_CLIENT_KEY') : ($_SESSION['gambio_hub_session_key'] ?? '')));
                if ($adminMessageSignature === $adminSignature
                    && !(isset($_SESSION['gambio_hub_error_processed'])
                         && $_SESSION['gambio_hub_error_processed'] === $adminMessageSignature)) {
                    $_SESSION['gambio_hub_error_processed'] = $adminMessageSignature;
                    if ($orderNumber > 0) {
                        $this->setOrderStatus($orderNumber, (int)($this->order_status ?? 0), $adminMessage);
                    }
                }
            }
        }
        
        return $error;
    }


	/**
	 * Returns whether the module is installed or not.
	 *
	 * @return bool|int
	 */
    public function check()
    {
        if (!isset ($this->_check)) {
            $sql = 'SELECT `value` FROM `gx_configurations` WHERE `key` = "configuration/MODULE_PAYMENT_GAMBIO_HUB_STATUS"';
            $check_query  = xtc_db_query($sql);
            $this->_check = xtc_db_num_rows($check_query);
        }

        return $this->_check;
    }


    /**
	 * Installation callback of the module.
	 */
	public function install()
	{
        $configuration = [
            [
                'key'          => 'configuration/MODULE_PAYMENT_GAMBIO_HUB_STATUS',
                'value'        => 'False',
                'default'      => 'False',
                'type'         => 'switcher',
            ],
            [
                'key'          => 'configuration/MODULE_PAYMENT_GAMBIO_HUB_ALLOWED',
                'value'        => '',
                'default'      => '',
                'type'         => null,
            ],
            [
                'key'          => 'configuration/MODULE_PAYMENT_GAMBIO_HUB_SORT_ORDER',
                'value'        => '0',
                'default'      => '0',
                'type'         => null,
            ],
            [
                'key'          => 'configuration/MODULE_PAYMENT_GAMBIO_HUB_ZONE',
                'value'        => '0',
                'default'      => '0',
                'type'         => 'geo-zone',
            ],
            [
                'key'          => 'configuration/MODULE_PAYMENT_GAMBIO_HUB_ORDER_STATUS_ID',
                'value'        => '0',
                'default'      => '0',
                'type'         => 'order-status',
            ],
            [
                'key'          => 'configuration/MODULE_PAYMENT_GAMBIO_HUB_URL',
                'value'        => 'https://core-api.gambiohub.com/a/api.php/api/v1',
                'default'      => 'https://core-api.gambiohub.com/a/api.php/api/v1',
                'type'         => null,
            ],
            [
                'key'          => 'configuration/MODULE_PAYMENT_GAMBIO_HUB_SETTINGS_APP_URL',
                'value'        => 'https://gui.gambiohub.com/a/settings',
                'default'      => 'https://gui.gambiohub.com/a/settings',
                'type'         => null,
            ],
            [
                'key'          => 'configuration/MODULE_PAYMENT_GAMBIO_HUB_ACCOUNT_APP_URL',
                'value'        => 'https://gui.gambiohub.com/a/account',
                'default'      => 'https://gui.gambiohub.com/a/account',
                'type'         => null,
            ],
            [
                'key'          => 'configuration/MODULE_PAYMENT_GAMBIO_HUB_IP_LIST_URL',
                'value'        => 'https://core-api.gambiohub.com/trust/hub_hosts.json',
                'default'      => 'https://core-api.gambiohub.com/trust/hub_hosts.json',
                'type'         => null,
            ],
            [
                'key'          => 'configuration/MODULE_PAYMENT_GAMBIO_HUB_DATA_OBSERVER',
                'value'        => 'True',
                'default'      => 'True',
                'type'         => 'switcher',
            ],
            [
                'key'          => 'configuration/MODULE_PAYMENT_GAMBIO_HUB_IP_WHITELIST',
                'value'        => '',
                'default'      => '',
                'type'         => null,
            ],
        ];

        $query = "INSERT INTO `gx_configurations` (`key`, `value`, `default`, `type`, `sort_order`) VALUES (':key', ':value', ':default', :type, ':sort_order')";
        
        foreach ($configuration as $sortOrder => $entry) {
            $entryQuery = strtr($query,
                                [
                                    ':key'          => $entry['key'],
                                    ':value'        => $entry['value'],
                                    ':default'      => $entry['default'],
                                    ':type'         => $entry['type'] ? "'" . $entry['type'] . "'" : 'null',
                                    ':sort_order'   => (string)$sortOrder,
                                ]);
            xtc_db_query($entryQuery);
        }


		if(!$this->_columnExists('gambio_hub_module'))
		{
			xtc_db_query("ALTER TABLE `orders` ADD COLUMN `gambio_hub_module` VARCHAR(32) DEFAULT ''");
		}

		if(!$this->_columnExists('gambio_hub_module_title'))
		{
			xtc_db_query("ALTER TABLE `orders` ADD COLUMN `gambio_hub_module_title` VARCHAR(255) DEFAULT ''");
		}

		if(!$this->_columnExists('gambio_hub_transaction_code'))
		{
			xtc_db_query("ALTER TABLE `orders` ADD COLUMN `gambio_hub_transaction_code` VARCHAR(64) DEFAULT ''");
		}
	}


	/**
	 * Removal callback of the module.
	 */
	public function remove()
	{
		// Remove all module-related configuration values from configuration table.
		xtc_db_query("DELETE FROM `gx_configurations`
					  WHERE `key` in ('" . implode("', '", $this->keys()) . "')");
	}


	/**
	 * Returns the module keys.
	 *
	 * @return array
	 */
	public function keys()
	{
        return [
			'configuration/MODULE_PAYMENT_GAMBIO_HUB_STATUS',
			'configuration/MODULE_PAYMENT_GAMBIO_HUB_ALLOWED',
			'configuration/MODULE_PAYMENT_GAMBIO_HUB_SORT_ORDER',
			'configuration/MODULE_PAYMENT_GAMBIO_HUB_ZONE',
			'configuration/MODULE_PAYMENT_GAMBIO_HUB_ORDER_STATUS_ID',
			'configuration/MODULE_PAYMENT_GAMBIO_HUB_URL',
			'configuration/MODULE_PAYMENT_GAMBIO_HUB_SETTINGS_APP_URL',
			'configuration/MODULE_PAYMENT_GAMBIO_HUB_ACCOUNT_APP_URL',
			'configuration/MODULE_PAYMENT_GAMBIO_HUB_IP_LIST_URL',
			'configuration/MODULE_PAYMENT_GAMBIO_HUB_IP_WHITELIST',
			'configuration/MODULE_PAYMENT_GAMBIO_HUB_DATA_OBSERVER',
		];
	}


	/**
	 * Payment action of the module.
	 *
	 * This method will start a new Gambio Hub transaction and store the transaction details to the database.
	 * The user will then be redirected to the payment site.
	 */
	public function payment_action()
	{
		try
		{
            if (empty($_SESSION['gambio_hub_selection'])) {
                throw new UnexpectedValueException('"gambio_hub_selection" is missing in the session');
            }
            
			// send additional parameters to hub
			if(isset($_POST[$_SESSION['gambio_hub_selection']]) && is_array($_POST[$_SESSION['gambio_hub_selection']]))
			{
				$moduleParameters     = $_POST[$_SESSION['gambio_hub_selection']];
				$hubSettings          = MainFactory::create('HubSettings', gm_get_conf('GAMBIO_HUB_CURL_TIMEOUT'));
				$hubCallbackApiClient = MainFactory::create('HubCallbackApiClient', MODULE_PAYMENT_GAMBIO_HUB_URL,
				                                            new CurlRequest(), LogControl::get_instance(),
				                                            $hubSettings);
				$hubCallbackApiClient->execute($_SESSION['gambio_hub_selection'], true, [
					'action' => 'moduleParameters',
				], [
					                               'sessionKey'       => $_SESSION['gambio_hub_session_key'] ?? null,
					                               'moduleParameters' => json_encode($moduleParameters),
				                               ]);
			}

			// Store the Gambio Hub Module in the database.
			$this->_storeGambioHubModule($GLOBALS['insert_id'], $_SESSION['gambio_hub_selection']);
            
            // Store the Gambio Hub Module title in the database.
            $moduleTitle = $_SESSION['gambio_hub_payments'][$_SESSION['gambio_hub_selection']]['title'];
            $this->_storeGambioHubModuleTitle($GLOBALS['insert_id'], $moduleTitle);

			// Hub transactions API client.
			$client = $this->_createHubTransactionsApiClient(new HubSessionKey($_SESSION['gambio_hub_session_key'] ?? ''));

			// Start the transaction and retrieve the transaction code.
			$transactionCode = $client->startTransaction($this->_createHubClientInformation(),
			                                             $this->_createOrderContent(),
			                                             $this->_createClientSessionInformation(new HubSessionKey($_SESSION['gambio_hub_session_key'] ?? '')));

			// Get the transaction details using the transaction code.
			$transactionDetails = $client->getTransactionDetails(new HubTransactionCode($transactionCode));

			// set order status
			$comment = array_key_exists('statusComment',
			                            $transactionDetails) ? $transactionDetails['statusComment'] : '';
			$this->setOrderStatus($GLOBALS['insert_id'], $transactionDetails['status'], $comment);

			// Payment instructions
			if(!empty($transactionDetails['paymentInstructions']))
			{
				xtc_db_perform('orders_payment_instruction', $transactionDetails['paymentInstructions']);
			}

			// Store the Gambio Hub transaction code in the database.
			$this->_storeGambioHubTransactionCode($GLOBALS['insert_id'], $transactionCode);

            // Store the Gambio Hub transaction mode in the database.
            $transactionMode = array_key_exists('mode', $transactionDetails)
                ? $transactionDetails['mode'] : 'live'; // if we dont have it, we assume live
            $this->_storeGambioHubTransactionMode($GLOBALS['insert_id'], $transactionMode);

			if(!empty($transactionDetails['errorMessage']))
			{
				throw new HubException($transactionDetails['errorMessage']);
			}

			// Transaction pay URL.
			$payUrl = $transactionDetails['transactionPayUrl'] ? : xtc_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL');

			// Payment instructions
			if(!empty($transactionDetails['payment_instructions']))
			{
				xtc_db_perform('orders_payment_instruction', $transactionDetails['payment_instructions']);
			}

			// Add the session in the transaction URL.
			$payUrl .= (!array_key_exists('query', parse_url($payUrl)) ? '?' : '&') . 'session_key='
			           . ($_SESSION['gambio_hub_session_key'] ?? '');

			unset($_SESSION['PayPal2Hub']);

			// Redirect to pay site.
			xtc_redirect($payUrl);
		}
		catch(UnexpectedValueException $e)
		{
            if (empty($_SESSION['gambio_hub_selection'])) {
                $cacheControl = MainFactory::create_object('CacheControl');
                $cacheControl->clear_data_cache();
            }
            
            $this->writeLog("gambio_hub->payment_action() failed with UnexpectedValueException '{$e->getMessage()}'.");
            
			// Redirect to checkout payment page, if Gambio Hub is not available
			$_SESSION['gambio_hub_error'] = MODULE_PAYMENT_GAMBIO_HUB_ERROR;
			xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'payment_error=' . $this->code, 'SSL'));
		}
		catch(CurlRequestException $e)
		{
            $this->writeLog("gambio_hub->payment_action() failed with CurlRequestException '{$e->getMessage()}'.");
            
			$_SESSION['gambio_hub_error'] = MODULE_PAYMENT_GAMBIO_HUB_ERROR;
			xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'payment_error=' . $this->code, 'SSL'));
		}
		catch(HubException $e)
		{
            $this->writeLog("gambio_hub->payment_action() failed with HubException '{$e->getMessage()}'.");
            
			$_SESSION['gambio_hub_error'] = $e->getMessage();
			xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'payment_error=' . $this->code, 'SSL'));
		}
	}


	protected function setOrderStatus($orders_id, $status_id, $comment = '')
	{
        /** @var OrderWriteServiceInterface $orderWriteService */
        $orderWriteService = StaticGXCoreLoader::getService('OrderWrite');
        $orderWriteService->updateOrderStatus(new IdType((int)$orders_id),
                                              new IntType((int)$status_id),
                                              new StringType((string)$comment),
                                              new BoolType(false));
	}


	/**
	 * Stores the Gambio Hub module to the orders table.
	 *
	 * @param int    $orderId The current order ID.
	 * @param string $module  The hub module name.
	 */
	protected function _storeGambioHubModule($orderId, $module)
	{
		$sql = '
			UPDATE `orders`
			SET `gambio_hub_module` = "' . xtc_db_input($module) . '"
			WHERE `orders_id` = ' . (int)$orderId;
		xtc_db_query($sql);
	}


	/**
	 * Stores the Gambio Hub module title to the orders table.
	 *
	 * @param int    $orderId     The current order ID.
	 * @param string $moduleTitle The hub module name.
	 */
	protected function _storeGambioHubModuleTitle($orderId, $moduleTitle)
	{
		$sql = '
			UPDATE `orders`
			SET `gambio_hub_module_title` = "' . xtc_db_input($moduleTitle) . '"
			WHERE `orders_id` = ' . (int)$orderId;
		xtc_db_query($sql);
	}


	/**
	 * Store the Gambio Hub transaction code to the orders table.
	 *
	 * @param int $orderId The current order ID.
	 * @param     string   gambio_hub_transaction_code  The hub transaction code.
	 */
	protected function _storeGambioHubTransactionCode($orderId, $transactionCode)
	{
		$sql = '
			UPDATE orders
			SET gambio_hub_transaction_code = "' . xtc_db_input($transactionCode) . '"
			WHERE orders_id = ' . (int)$orderId;
		xtc_db_query($sql);
	}

    /**
     * Store the Gambio Hub transaction mode to the orders table.
     *
     * @param int $orderId The current order ID.
     * @param string   gambio_hub_transaction_mode  The hub transaction mode.
     */
    protected function _storeGambioHubTransactionMode($orderId, $transactionMode)
    {
        $sql = '
			UPDATE orders
			SET gambio_hub_transaction_mode = "' . xtc_db_input($transactionMode) . '"
			WHERE orders_id = ' . (int)$orderId;
        xtc_db_query($sql);
    }


	/**
	 * Check if given column exists in the orders table.
	 *
	 * @param $column
	 *
	 * @return bool
	 */
	protected function _columnExists($column)
	{
		$sql   = 'SHOW COLUMNS IN orders LIKE "' . xtc_db_input($column) . '"';
		$query = xtc_db_query($sql);

		return xtc_db_num_rows($query) > 0;
	}


    /**
     * Checks if given table exists in the database.
     *
     * @param $table
     *
     * @return bool
     */
    protected function _tableExists($table)
    {
        $sql = "SHOW TABLES LIKE '" . $table . "'";
        $result = xtc_db_query($sql);
        $exists = false;
        while (xtc_db_fetch_array($result)) {
            $exists = true;
        }
        return $exists;
    }


	/**
	 * Creates a HubClientInformation instance.
	 *
	 * @return  \HubPublic\ValueObjects\HubClientInformation Created instance.
	 */
	protected function _createHubClientInformation()
	{
		/**
		 * Hub client key configuration.
		 *
		 * @var HubClientKeyConfiguration $hubClientKeyConfiguration
		 */
		$hubClientKeyConfiguration = MainFactory::create('HubClientKeyConfiguration');

		// Hub client key.
		$hubClientKey = new HubClientKey($hubClientKeyConfiguration->get());

		// Client version.
		$shopVersion = ltrim(gm_get_conf('INSTALLED_VERSION'), 'v');

		// Client URL.
		$shopUrl = GM_HTTP_SERVER . DIR_WS_CATALOG;

		return new HubClientInformation($hubClientKey, $shopVersion, $shopUrl);
	}


	/**
	 * Creates a HubTransactionsApiClient instance.
	 *
	 * @param \HubPublic\ValueObjects\HubSessionKey $sessionKey Hub session key.
	 *
	 * @return \HubTransactionsApiClient Created instance.
	 */
	protected function _createHubTransactionsApiClient($sessionKey)
	{
		// cURL request.
		$request = new CurlRequest();

		// Cart content serializer.
		$cartContentSerializer = new CartContentSerializer();

		// Customer information serializer.
		$customerInformationSerializer = new CustomerInformationSerializer();

		// Hub client information serializer.
		$hubClientInformationSerializer = new HubClientInformationSerializer();

		// Session information serializer.
		$clientSessionInformationSerializer = new ClientSessionInformationSerializer();

		// Order content serializer.
		$orderContentSerializer = new OrderContentSerializer($customerInformationSerializer);

		// Shop logger instance.
		$logControl = LogControl::get_instance();

		// Hub settings instance.
		$hubSettings = MainFactory::create('HubSettings', gm_get_conf('GAMBIO_HUB_CURL_TIMEOUT'));

		return MainFactory::create('HubTransactionsApiClient', MODULE_PAYMENT_GAMBIO_HUB_URL, $sessionKey, $request,
		                           $cartContentSerializer, $customerInformationSerializer,
		                           $hubClientInformationSerializer, $clientSessionInformationSerializer,
		                           $orderContentSerializer, $logControl, $hubSettings);
	}


	/**
	 * Creates a OrderContent instance.
	 *
	 * @return \HubPublic\ValueObjects\OrderContent Created instance.
	 */
	protected function _createOrderContent()
	{
		// Submitted order content.
		$order = $GLOBALS['order'];

		// Submitted order info content.
		$orderInfo = $order->info;

		// Submitted billing content.
		$billing = $order->billing;

		// Submitted customer content.
		$customer = $order->customer;

		// Submitted shipping content.
		$shipping = $order->delivery;

		// Order ID.
		$orderId = $GLOBALS['insert_id'];

		// Customer's ID.
		$customerId = $_SESSION['customer_id'];

		// Missing order content information.
		$missingInformation = $this->_getMissingOrderContentInformation($orderId, $customerId);

		// Formatted date time string.
		$customerDateOfBirthFormatted = new DateTime($missingInformation['customer']['customers_dob']);
		$customerDateOfBirthFormatted = $customerDateOfBirthFormatted->format('Y-m-d');

		// Parsed order created date time.
		$orderCreatedDateTimeParsed = new DateTime($missingInformation['order']['date_purchased']);

		// CustomerInformation instance builder.
		$customerInformationBuilder = new CustomerInformationBuilder();

		// Insert billing values to CustomerInformation instance.
		$customerInformationBuilder->setBillingAddress1("{$billing['street_address']} {$billing['house_number']}")
		                           ->setBillingAddress2((string)$billing['additional_address_info'])
		                           ->setBillingCity((string)$billing['city'])
		                           ->setBillingCompany((string)$billing['company'])
		                           ->setBillingCountry((string)$billing['country']['title'])
		                           ->setBillingCountryCode((string)$billing['country']['iso_code_2'])
		                           ->setBillingFirstName((string)$billing['firstname'])
		                           ->setBillingGender((string)$billing['gender'])
		                           ->setBillingLastName((string)$billing['lastname'])
		                           ->setBillingPostalCode((string)$billing['postcode'])
		                           ->setBillingState((string)$billing['state'])
		                           ->setBillingTitle((string)$billing['gender'] === 'm' ? MALE : FEMALE);

		// Insert customer values to CustomerInformation instance.
		$customerInformationBuilder->setCustomerAddress1("{$customer['street_address']} {$customer['house_number']}")
		                           ->setCustomerAddress2((string)$customer['additional_address_info'])
		                           ->setCustomerCity((string)$customer['city'])
		                           ->setCustomerCompany((string)$customer['company'])
		                           ->setCustomerCountry((string)$customer['country']['title'])
		                           ->setCustomerCountryCode((string)$customer['country']['iso_code_2'])
		                           ->setCustomerDateOfBirth((string)$customerDateOfBirthFormatted)
		                           ->setCustomerEmail((string)$customer['email_address'])
		                           ->setCustomerFax((string)$missingInformation['customer']['customers_fax'])
		                           ->setCustomerFirstName((string)$customer['firstname'])
		                           ->setCustomerGender((string)$customer['gender'])
		                           ->setCustomerLastName((string)$customer['lastname'])
		                           ->setCustomerNumber((string)$customerId)
		                           ->setCustomerPhone((string)$customer['telephone'])
		                           ->setCustomerPostalCode((string)$customer['postcode'])
		                           ->setCustomerState((string)$customer['state'])
		                           ->setCustomerTitle((string)$customer['gender'] === 'm' ? MALE : FEMALE);

		// Insert shipping values to CustomerInformation instance.
		$customerInformationBuilder->setShippingAddress1("{$shipping['street_address']} {$shipping['house_number']}")
		                           ->setShippingAddress2((string)$shipping['additional_address_info'])
		                           ->setShippingCity((string)$shipping['city'])
		                           ->setShippingCompany((string)$shipping['company'])
		                           ->setShippingCountry((string)$shipping['country']['title'])
		                           ->setShippingCountryCode((string)$shipping['country']['iso_code_2'])
		                           ->setShippingFirstName((string)$shipping['firstname'])
		                           ->setShippingGender((string)$shipping['gender'])
		                           ->setShippingLastName((string)$shipping['lastname'])
		                           ->setShippingPostalCode((string)$shipping['postcode'])
		                           ->setShippingState((string)$shipping['state'])
		                           ->setShippingTitle((string)$shipping['gender'] === 'm' ? MALE : FEMALE);

		// Build CustomerInformation instance.
		$customerInformation = $customerInformationBuilder->build();

		// OrderContent class builder.
		$orderContentBuilder = new OrderContentBuilder();

		$total = $orderInfo['total'];
		if($_SESSION['customers_status']['customers_status_show_price_tax'] === '0'
		   && $_SESSION['customers_status']['customers_status_add_tax_ot'] === '1')
		{
			$total += $orderInfo['tax'];
		}
        
        if (empty($_SESSION['gambio_hub_selection'])) {
            throw new UnexpectedValueException('"gambio_hub_selection" is missing in the session');
        }

		// Insert values to OrderContent class.
		$orderContentBuilder->setAmount(round($total, 2))
		                    ->setCurrencyCode($orderInfo['currency'])
		                    ->setCustomer($customerInformation)
		                    ->setCustomerNumber($customerId)
		                    ->setLanguageCode($_SESSION['language_code'])
		                    ->setOrderDateTime($orderCreatedDateTimeParsed)
		                    ->setOrderNumber($orderId)
		                    ->setPaymentMethod((string)$_SESSION['gambio_hub_selection'])
		                    ->setShippingMethod($orderInfo['shipping_class'] ?? '');

		return $orderContentBuilder->build();
	}


	/**
	 * Returns the missing order content information.
	 *
	 * @param int $orderId    Order ID to fetch the missing information for.
	 * @param int $customerId Customer ID to fetch the missing information for.
	 *
	 * @return array
	 *
	 * @throws InvalidArgumentException If the provided arguments are not valid ID values.
	 */
	protected function _getMissingOrderContentInformation($orderId, $customerId)
	{
		if(!is_numeric($orderId) || (int)$orderId != $orderId || is_float($orderId) || $orderId < 0)
		{
			throw new InvalidArgumentException('Invalid $orderId argument value: ' . $orderId);
		}

		if(!is_numeric($customerId) || (int)$customerId != $customerId || is_float($customerId) || $customerId < 0)
		{
			throw new InvalidArgumentException('Invalid $customerId argument value: ' . $customerId);
		}

		// Data array which will be returned.
		$result = [];

		// Query to fetch the missing information an order.
		$orderQuery = xtc_db_query('
			SELECT `date_purchased`
			FROM `orders`
			WHERE `orders_id` = ' . (int)$orderId . '
		');

		// Fetch order query result.
		$orderQueryResult = xtc_db_fetch_array($orderQuery);

		// Add order result to data array.
		$result['order'] = $orderQueryResult;

		// Query to fetch the missing information a customer.
		$customerQuery = xtc_db_query('
			SELECT `customers_fax`, `customers_dob`
			FROM `customers`
			WHERE `customers_id` = ' . (int)$customerId . '
		');

		// Fetch customer query result.
		$customerQueryResult = xtc_db_fetch_array($customerQuery);

		// Add customer result to data array.
		$result['customer'] = $customerQueryResult;

		return $result;
	}


	/**
	 * Creates a ClientSessionInformation instance.
	 *
	 * @param \HubPublic\ValueObjects\HubSessionKey $sessionKey Hub session key.
	 *
	 * @return \HubPublic\ValueObjects\ClientSessionInformation Created instance.
	 */
	protected function _createClientSessionInformation($sessionKey)
	{
		// Language code.
		$languageCode = $_SESSION['language_code'];

		// Currency.
		$currency = $_SESSION['currency'];

		// User IP address.
		$userIp = $_SERVER['REMOTE_ADDR'];

		// User agent.
		$userAgent = $_SERVER['HTTP_USER_AGENT'];

		return new ClientSessionInformation($sessionKey, $languageCode, $currency, $userIp, $userAgent);
	}
    
    
    /**
     * @param string $message
     *
     * @return void
     */
    protected function writeLog(string $message): void
    {
        $logControl = LogControl::get_instance();
        $logControl->notice($message . ' Customer was redirected to checkout payment page to try it again.',
                            '',
                            'hub',
                            'notice',
                            'USER NOTICE',
                            0,
                            'Gambio Hub Session-Key: ' . var_export($_SESSION['gambio_hub_session_key'] ??
                                                                    '--missing--',
                                                                    true) . "\n" . '$_SESSION[\'payment\']: '
                            . var_export($_SESSION['payment'], true) . "\n" . '$_SESSION[\'gambio_hub_selection\']: '
                            . var_export($_SESSION['gambio_hub_selection'] ?? '--missing--', true) . "\n"
                            . '$_SESSION[\'gambio_hub_subselection\']: '
                            . var_export($_SESSION['gambio_hub_subselection'] ?? '--missing--', true) . "\n");
    }
}

MainFactory::load_origin_class('gambio_hub');
