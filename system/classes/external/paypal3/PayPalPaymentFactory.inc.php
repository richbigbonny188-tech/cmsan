<?php
/* --------------------------------------------------------------
	PayPalPaymentFactory.inc.php 2020-06-23
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2018 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * Factory class for the creation of new PayPal payments
 */
class PayPalPaymentFactory
{
	/**
	 * @var \PayPalConfigurationStorage $config configuration
	 */
	protected $config;

	/**
	 * @var PayPalEncodingHelper $encHelper
	 */
	protected $encHelper;
	
	/**
	 * @var \PayPalLogger
	 */
	protected $logger;

	/**
	 * constructor; initializes configuration and encoding helper
	 */
	public function __construct()
	{
		$this->config = MainFactory::create('PayPalConfigurationStorage');
		$this->encHelper = MainFactory::create('PayPalEncodingHelper');
		$this->logger = MainFactory::create('PayPalLogger');
	}

	/**
	 * creates a PayPal payment from a Gambio order
	 * @param order $order an order object for which to create a payment
	 * @param string $mode payment mode (ecm|ecs|plus)
	 * @param string $state state for shipping address
	 * @return PayPalPayment newly created PayPalPayment object
	 * @throws Exception if payment cannot be created
	 */
	public function createPaymentFromOrder(order $order, $mode = 'ecm', $state = null)
	{
		require_once DIR_FS_CATALOG . 'gm/classes/JSON.php';
		$json            = MainFactory::create('Services_JSON');
		$paymentArray    = $this->makePaymentArrayFromOrder($order, $mode, $state);
		$paymentJSON     = $json->encodeUnsafe($paymentArray);
		$ppRestService   = MainFactory::create_object('PayPalRestService');
		$ppRestRequest   = MainFactory::create_object('PayPalRestRequest',
		                                              ['POST', '/v1/payments/payment', $paymentJSON, $mode]);
		$response        = $ppRestService->performRequest($ppRestRequest);
		$response_object = $response->getResponseObject();
		if($response_object === false)
		{
			throw new Exception('Error decoding response ' . print_r($response, true));
		}
		if((int)$response->getResponseCode() !== 201)
		{
			$error_message = $response_object->name.' '.$response_object->message;
			$detailMessages = [];
			
			if(isset($response_object->details))
			{
				foreach($response_object->details as $detail)
				{
					$detailMessages[] = $detail->field .': '.$detail->issue;
				}
				$error_message .= implode('; ', $detailMessages);
			}
			
			$exception = MainFactory::create('PayPalCreatePaymentException', 'ERROR creating payment: ' . $error_message);
			#$exception->setUserMessage(implode('; ', $detailMessages));
			throw $exception;
		}

		$paypalPayment = MainFactory::create_object('PayPalPayment', array($response_object));
		return $paypalPayment;
	}
	
	
	/**
	 * formats payment amounts for transmission
	 *
	 * @param float $amount
	 * @param string $currency
	 *
	 * @return string
	 */
	protected function _formatAmount($amount, $currency)
	{
		$amount = (float)$amount;
		$noDecimalsCurrencies = ['HUF', 'TWD'];
		if(in_array($currency, $noDecimalsCurrencies, true))
		{
			$decimalDigits = 0;
		}
		else
		{
			$decimalDigits = 2;
		}
		$formattedAmount = number_format($amount, $decimalDigits, '.', '');
		return $formattedAmount;
	}


	/**
	 * prepares data to be encoded in JSON for the creation of a new order.
	 * In Plus mode only data directly related to the payment is added; customers personal data is added later.
	 * @param order $order
	 * @param string $mode payment mode (ecm|ecs|plus)
	 * @param string $state state for shipping address
	 * @return array data to be sent to PayPal
	 */
	public function makePaymentArrayFromOrder(order $order, $mode = 'ecm', $state = null, $countryCode = null)
	{
		$itemsAndSubtotal = $this->makeItemsAndSubtotalFromOrder($order);
		$itemsArray       = $itemsAndSubtotal['items'];
		$subtotal         = $itemsAndSubtotal['subtotal'];
		$intent           = $mode
		                    === 'installments' ? $this->config->get('intent_installments') : $this->config->get('intent');
		$session_shipping = $_SESSION['shipping'];

        $shippingCost             = 0;
        $cartShippingCostsControl = MainFactory::create_object('CartShippingCostsControl', [], true);
        $selectedShippingModuleArray = $cartShippingCostsControl->get_selected_shipping_module();
        [$selectedShippingModule, $selectedShippingMethod] = explode('_', key($selectedShippingModuleArray));
        $selectedShippingModuleLabel = current($selectedShippingModuleArray);
        $shipping             = MainFactory::create('shipping');
        $selectedModuleQuote  = $shipping->quote($selectedShippingMethod, $selectedShippingModule);
        $_SESSION['shipping'] = [
            'id'    => $selectedShippingModule . '_' . $selectedShippingMethod,
            'title' => $selectedShippingModuleLabel,
        ];
        if (!empty($selectedModuleQuote)) {
            foreach ($selectedModuleQuote[0]['methods'] as $methodArray) {
                if ($methodArray['id'] === $selectedShippingMethod) {
                    $_SESSION['shipping']['cost'] = $methodArray['cost'];
                }
            }
        }
        
        $globals_order       = $GLOBALS['order'];
        $GLOBALS['order']    = new order();
        $session_payment     = $_SESSION['payment'];
        $_SESSION['payment'] = 'paypal3';
        $t_order_total = new order_total();
        $t_order_total->collect_posts();
        $t_order_total->pre_confirmation_check();
        $t_order_total_array = $t_order_total->process();
        $total               = $GLOBALS['order']->info['total'];
        foreach ($t_order_total_array as $totalsIdx => $totalsEntry) {
            if ($totalsEntry['code'] === 'ot_shipping') {
                $shippingCost = (float)$totalsEntry['value'];
            }
        }

		$_SESSION['shipping'] = $session_shipping;
		$_SESSION['payment']  = $session_payment;

		$tax = 0;
		if ((int)$_SESSION['customers_status']['customers_status_show_price_tax'] === 0
            && (int)$_SESSION['customers_status']['customers_status_add_tax_ot'] === 1) {
            $tax = $GLOBALS['order']->info['tax'];
            $total += round($tax, 2);
        }
        
        $GLOBALS['order']     = $globals_order;
        
        if($this->config->get('mode') === 'live')
		{
			$paymentExperienceProfileId = $this->config->get('payment_experience_profile/'.$_SESSION['language_code']);
		}
		else
		{
			$paymentExperienceProfileId = $this->config->get('payment_experience_profile_sandbox/'.$_SESSION['language_code']);
		}
		$paymentArray = [
			'intent'       => $intent,
			'payer'        => [
				'payment_method' => 'paypal',
			],
			'transactions'  => [
				[
					'amount'      => [
						'currency' => $order->info['currency'],
						'total'    => $this->_formatAmount($total, $order->info['currency']),
						'details'  => [
							'subtotal' => $this->_formatAmount($subtotal, $order->info['currency']),
							'tax'      => $this->_formatAmount($tax, $order->info['currency']),
						],
					],
					'description' => mb_substr($this->encHelper->transcodeOutbound(STORE_NAME), 0, 120),
					'item_list'   => [
						'items' => $itemsArray,
					],
				],
			],
			'redirect_urls' => $this->getRedirectUrls($mode),
		];
		if($mode !== 'installments' && !empty($paymentExperienceProfileId))
		{
			$paymentArray['experience_profile_id'] = $paymentExperienceProfileId;
		}
		elseif($mode === 'installments')
		{
			$paymentArray['application_context'] = [
				'locale'      => 'DE',
				'user_action' => 'continue',
			];
		}

		$isB2B = (bool)$_SESSION['customer_b2b_status'] === true || !empty($GLOBALS['order']->billing['company']);
		$cartContentType = $_SESSION['cart']->get_content_type();
		$requireInstantFunding = $cartContentType === 'virtual' || $cartContentType === 'mixed'
		                         || (string)$this->config->get('require_instant_funding') === 'always'
		                         || ($isB2B === true && (string)$this->config->get('require_instant_funding') === 'b2b');
		$this->logger->debug_notice(
			sprintf("INSTANT_FUNDING:\nContentType: %s\nIsB2B: %s\nCompany: %s\n",
			        $cartContentType,
			        $isB2B ? 'yes' : 'no',
			        $GLOBALS['order']->billing['company']
			)
		);
		if($requireInstantFunding === true)
		{
			$paymentArray['transactions'][0]['payment_options'] = [
				'allowed_payment_method' => 'INSTANT_FUNDING_SOURCE',
			];
		}

		if($shippingCost > 0)
		{
			$paymentArray['transactions'][0]['amount']['details']['shipping'] = $this->_formatAmount($shippingCost, $order->info['currency']);
		}

		$remaining_fee = round($total, 2) - round($shippingCost, 2) - round($subtotal, 2) - round($tax, 2);
		$remaining_fee = round($remaining_fee, 2);

		if($remaining_fee > 0)
		{
			$paymentArray['transactions'][0]['amount']['details']['handling_fee'] = $this->_formatAmount($remaining_fee, $order->info['currency']);
		}
		elseif($remaining_fee < 0)
		{
			$paymentArray['transactions'][0]['amount']['details']['shipping_discount'] = $this->_formatAmount($remaining_fee, $order->info['currency']);
		}
        
        $hasDeliveryAddress =
            (!empty($order->delivery['lastname']) || !empty($order->delivery['company']))
               && !empty($order->delivery['street_address'])
               && !empty($order->delivery['city']);
		if($mode !== 'plus' && $hasDeliveryAddress)
		{
			$shippingCountryCode = mb_substr($order->delivery['country']['iso_code_2'], 0, 2);
			if (!empty($order->delivery['lastname'])) {
                $recipientName = mb_substr($this->encHelper->transcodeOutbound($order->delivery['firstname'] . ' '
                                                                               . $order->delivery['lastname']), 0, 50);
            } else {
                $recipientName = mb_substr($this->encHelper->transcodeOutbound($order->delivery['company']), 0, 50);
            }
			$paymentArray['transactions'][0]['item_list']['shipping_address'] = [
				'recipient_name' => $recipientName,
				'line1'          => mb_substr($this->encHelper->transcodeOutbound($order->delivery['street_address']), 0,
				                           100),
				'city'           => mb_substr($this->encHelper->transcodeOutbound($order->delivery['city']), 0, 50),
				'postal_code'    => mb_substr($this->encHelper->transcodeOutbound($order->delivery['postcode']), 0, 20),
				'country_code'   => $shippingCountryCode,
			];
			if(!empty($order->delivery['house_number']))
			{
				$paymentArray['transactions'][0]['item_list']['shipping_address']['line1'] .= ' ' . $order->delivery['house_number'];
			}
			if($state !== null)
			{
				$paymentArray['transactions'][0]['item_list']['shipping_address']['state'] = mb_substr($state, 0, 100);
			}
			else
			{
				$paymentArray['transactions'][0]['item_list']['shipping_address']['state'] = mb_substr($order->delivery['state'], 0, 100);
			}
		}

		if($mode === 'installments')
		{
			$paymentArray['payer']['external_selected_funding_instrument_type'] = 'CREDIT';
			unset($paymentArray['experience_profile_id']);
		}

		return $paymentArray;
	}

	/**
	 * extracts data for line items and resulting subtotal from an order
	 * @param order $order
	 * @return array with keys 'items' (array of line items) and 'subtotal' (float)
	 */
	protected function makeItemsAndSubtotalFromOrder(order $order)
	{
		$subtotal = 0;
		$itemsArray = array();
		foreach($order->products as $product)
		{
			$quantity_name = $this->getQuantityName($product['quantity_unit_id'], $_SESSION['languages_id']);
			if(($product['qty'] - floor($product['qty'])) > 0)
			{
				$qty = 1;
				$qty_prefix = str_replace('.', ',', (string)$product['qty']);
				$price = $product['qty'] * $product['price'];
			}
			else
			{
				$qty = $product['qty'];
				$qty_prefix = '';
				$price = $product['price'];
			}

			$qty_suffix = $quantity_name;
			if(!empty($qty_prefix))
			{
				$qty_suffix = $qty_prefix . ' ' . $qty_suffix;
			}
			$qty_suffix = empty($qty_suffix) ? '' : ' (' . $qty_suffix . ')';

			if(empty($product['name']))
			{
				$product['name'] = 'unnamed_product_with_id_'.$product['id'];
			}

			$productItem = array(
				'quantity' => $qty,
				'name' => mb_substr($this->encHelper->transcodeOutbound($product['name'] . $qty_suffix), 0, 127),
				'price' => $this->_formatAmount($price, $order->info['currency']),
				'currency' => $order->info['currency'],
			);
			if(!empty($product['model']))
			{
				$productItem['sku'] = mb_substr($this->encHelper->transcodeOutbound($product['model']), 0, 50);
			}
			$itemsArray[] = $productItem;
			$subtotal += $qty * round($price, 2);
		}

		$itemsAndSubtotal = array(
				'items' => $itemsArray,
				'subtotal' => $subtotal,
			);
		return $itemsAndSubtotal;
	}
	
	
	/**
	 * retrieves quantity unit name to be used as a prefix for the line item name
	 *
	 * @param $quantity_unit_id int
	 * @param $languages_id     int
	 *
	 * @return string
	 */
	protected function getQuantityName($quantity_unit_id, $languages_id)
	{
		$quantityUnitName = '';
		try
		{
			$quantityUnitReadService = StaticGXCoreLoader::getService('QuantityUnitRead');
			$quantityUnit            = $quantityUnitReadService->getById(new IdType((int)$quantity_unit_id));
			$languageHelper          = MainFactory::create('LanguageHelper',
			                                               StaticGXCoreLoader::getDatabaseQueryBuilder());
			$languageCode            = $languageHelper->getLanguageCodeById(new IdType($languages_id));
			$quantityUnitName        = $quantityUnit->getName($languageCode);
		}
		catch(EntityNotFoundException $e)
		{
			// pass
		}
		
		return $quantityUnitName;
	}

	/**
	 * creates URLs for redirection back from PayPal hosted payment pages to the shop
	 * @param string $mode payment mode (ecm|ecs|plus)
	 * @return array with keys 'return_url' and 'cancel_url'
	 */
	protected function getRedirectUrls($mode)
	{
		if($mode != 'ecs')
		{
			$returnUrls = array(
				"return_url" => str_replace('&amp;', '&', xtc_href_link('checkout_confirmation.php', '', 'SSL')),
				"cancel_url" => str_replace('&amp;', '&', xtc_href_link('checkout_payment.php', 'paypal=cancel', 'SSL')),
			);
		}
		else
		{
			$returnUrls = array(
				"return_url" => str_replace('&amp;', '&', xtc_href_link('shop.php', 'do=PayPal/ReturnFromECS', 'SSL')),
				"cancel_url" => str_replace('&amp;', '&', xtc_href_link('shop.php', 'do=PayPal/CancelECS', 'SSL')),
			);
		}
		return $returnUrls;
	}

	/**
	 * updates an existing PayPal payment from a given order.
	 * required to add shipping address to a Plus payment if customer chooses PayPal
	 * @param string $payment_id ID of payment resource to update
	 * @param order $order
	 * @throws Exception if updating payment fails
	 */
	public function updatePaymentFromOrder($payment_id, order $order, $installmentsMode = false)
	{
		require_once DIR_FS_CATALOG.'gm/classes/JSON.php';
		$json = MainFactory::create('Services_JSON');
		
		$patchArray = array();
		
		if($installmentsMode === false && $_SESSION['cart']->get_content_type() !== 'virtual')
		{
			$this->logger->notice(sprintf('Updating non-virtual payment %s with shipping_address', $payment_id));
			$shippingCountryCode = isset($order->delivery['country']['iso_code_2']) ? $order->delivery['country']['iso_code_2'] : $order->delivery['country_iso_2'];
			$shippingLine2 = $order->delivery['company'] ?: '';
			$shippingLine2 .= (empty($shippingLine2) || empty($order->delivery['additional_address_info'])) ? '' : ', ';
			$shippingLine2 .= $order->delivery['additional_address_info'] ?: '';
			$patchArray[] = array(
				'op'    => 'add',
				'path'  => '/transactions/0/item_list/shipping_address',
				'value' => array(
					'recipient_name' => $this->encHelper->transcodeOutbound($order->delivery['firstname'] . ' ' . $order->delivery['lastname']),
					'line1'          => $this->encHelper->transcodeOutbound($order->delivery['street_address']),
					'line2'          => $shippingLine2,
					'city'           => $this->encHelper->transcodeOutbound($order->delivery['city']),
					'postal_code'    => $this->encHelper->transcodeOutbound($order->delivery['postcode']),
					'country_code'   => $shippingCountryCode,
				)
			);
			if(!empty($order->delivery['house_number']))
			{
				$patchArray[0]['value']['line1'] .= ' ' . $order->delivery['house_number'];
			}
			if(!empty($order->delivery['state']))
			{
				$patchArray[0]['value']['state'] = $this->encHelper->transcodeOutbound($order->delivery['state']);
                $patchArray[0]['value']['state'] = mb_substr($patchArray[0]['value']['state'], 0, 40);
			}
			else if(!empty($_SESSION['paypal_state']))
			{
				$patchArray[0]['value']['state'] = $this->encHelper->transcodeOutbound($_SESSION['paypal_state']);
                $patchArray[0]['value']['state'] = mb_substr($patchArray[0]['value']['state'], 0, 40);
			}
            $patchArray[0]['value']['recipient_name'] = mb_substr($patchArray[0]['value']['recipient_name'], 0, 128);
            $patchArray[0]['value']['line1']          = mb_substr($patchArray[0]['value']['line1'], 0, 100);
            $patchArray[0]['value']['line2']          = mb_substr($patchArray[0]['value']['line2'], 0, 100);
            $patchArray[0]['value']['city']           = mb_substr($patchArray[0]['value']['city'], 0, 64);
            $patchArray[0]['value']['postal_code']    = mb_substr($patchArray[0]['value']['postal_code'], 0, 60);
		}

		if($installmentsMode === false)
		{
			$this->logger->notice(sprintf('Updating billing_address for payment %s', $payment_id));
			$billingCountryCode = isset($order->billing['country']['iso_code_2']) ? $order->billing['country']['iso_code_2'] : $order->billing['country_iso_2'];
			$billingLine2 = $order->billing['company'] ?: '';
			$billingLine2 .= (empty($billingLine2) || empty($order->billing['additional_address_info'])) ? '' : ', ';
			$billingLine2 .= $order->billing['additional_address_info'] ?: '';
			$patchArray[] = array(
				'op'    => 'add',
				'path'  => '/potential_payer_info/billing_address',
				'value' => array(
					'line1'        => $this->encHelper->transcodeOutbound($order->billing['street_address']),
					'line2'        => $billingLine2,
					'city'         => $this->encHelper->transcodeOutbound($order->billing['city']),
					'postal_code'  => $this->encHelper->transcodeOutbound($order->billing['postcode']),
					'country_code' => $billingCountryCode,
				)
			);
			if(!empty($order->billing['house_number']))
			{
				$patchArray[1]['value']['line1'] .= ' ' . $order->billing['house_number'];
			}
            $patchArray[1]['value']['line1']          = mb_substr($patchArray[1]['value']['line1'], 0, 100);
            $patchArray[1]['value']['line2']          = mb_substr($patchArray[1]['value']['line2'], 0, 100);
            $patchArray[1]['value']['city']           = mb_substr($patchArray[1]['value']['city'], 0, 64);
            $patchArray[1]['value']['postal_code']    = mb_substr($patchArray[1]['value']['postal_code'], 0, 60);
		}

		if((bool)$this->config->get('add_orders_id_as_invoice_number') === true)
		{
			$this->logger->notice(sprintf('Adding orders_id %s to payment %s as invoice_number',
				(string)$order->info['orders_id'], $payment_id));
			$patchArray[] = array(
				'op'    => 'add',
				'path'  => '/transactions/0/invoice_number',
				'value' => mb_substr((string)$order->info['orders_id'], 0, 127),
			);
		}
		
		if((bool)$this->config->get('add_orders_id_as_custom') === true)
		{
			$this->logger->notice(sprintf('Adding orders_id %s to payment %s as custom',
			                              (string)$order->info['orders_id'], $payment_id));
			$patchArray[] = array(
				'op'    => 'add',
				'path'  => '/transactions/0/custom',
				'value' => mb_substr((string)$order->info['orders_id'], 0, 127),
			);
		}
		
		if((bool)$this->config->get('add_orders_id_as_soft_descriptor') === true)
		{
			$this->logger->notice(sprintf('Adding orders_id %s to payment %s as soft_descriptor',
			                              (string)$order->info['orders_id'], $payment_id));
			$patchArray[] = array(
				'op'    => 'add',
				'path'  => '/transactions/0/soft_descriptor',
				'value' => mb_substr((string)$order->info['orders_id'], 0, 22),
			);
		}
		
		/*
		// do not use, it’s broken: https://github.com/paypal/paypal-checkout/issues/319 (yes, still!)
		$patchArray[] = array(
			"op" => "add",
			"path" => "/transactions/0/purchase_order",
			"value" => substr((string)$order->info['orders_id'], 0, 127),
		);
		*/
		
		$patchJSON = $json->encodeUnsafe($patchArray);

		$mode = 'ecm';
		if(isset($_SESSION['paypal_payment']) && $_SESSION['paypal_payment']['id'] == $payment_id)
		{
			$mode = $_SESSION['paypal_payment']['type'];
		}
		if(isset($_SESSION['paypal_payment_installments']) && $_SESSION['paypal_payment_installments']['id'] == $payment_id)
		{
			$mode = $_SESSION['paypal_payment_installments']['type'];
		}
		$ppRestService = MainFactory::create('PayPalRestService');
		$patchPaymentRequest = MainFactory::create('PayPalRestRequest', 'PATCH', '/v1/payments/payment/'.$payment_id, $patchJSON, $mode);
		$response = $ppRestService->performRequest($patchPaymentRequest);
		if(!in_array($response->getResponseCode(), [200, 204], true))
		{
			$response_object = $response->getResponseObject();
			if($response_object === false)
			{
				throw new Exception('Error decoding response '.print_r($response, true));
			}
			
			if($response->getResponseCode() === 400
               && (string)$response_object->name === 'VALIDATION_ERROR'
               && in_array((string)$response_object->details[0]->field, ['city', 'state', 'zip'], true))
			{
			    $exception = MainFactory::create('PayPalInvalidAddressException', print_r($response_object, true));
			    throw $exception;
            }
			$error_message = $response_object->name.' '.$response_object->message;

			if(isset($response_object->details))
			{
				$error_message .= ', details: ';
				foreach($response_object->details as $detail)
				{
					$error_message .= $detail->field .': '.$detail->issue;
				}
			}
			throw new Exception('ERROR updating payment: '.$error_message);
		}
	}

	/**
	 * adds invoice_number to payment resource
	 */
	public function addInvoiceNumber($payment_id, order $order)
	{
		require_once DIR_FS_CATALOG.'gm/classes/JSON.php';
		$json = MainFactory::create('Services_JSON');
		$patchArray[] = [
			'op'    => 'add',
			'path'  => '/transactions/0/invoice_number',
			'value' => (string)$order->info['orders_id'],
		];
		$patchJSON = $json->encodeUnsafe($patchArray);
		$mode = isset($_SESSION['paypal_payment']['type']) ? $_SESSION['paypal_payment']['type'] : 'ecm';
		$ppRestService = MainFactory::create('PayPalRestService');
		$patchPaymentRequest = MainFactory::create('PayPalRestRequest', 'PATCH', '/v1/payments/payment/' . $payment_id,
		                                           $patchJSON, $mode);
		$response = $ppRestService->performRequest($patchPaymentRequest);
		if(!in_array($response->getResponseCode(), [200, 204], true))
		{
			$response_object = $response->getResponseObject();
			if($response_object === false)
			{
				throw new Exception('Error decoding response '.print_r($response, true));
			}
			$error_message = $response_object->name.' '.$response_object->message;

			if(isset($response_object->details))
			{
				$error_message .= ', details: ';
				foreach($response_object->details as $detail)
				{
					$error_message .= $detail->field .': '.$detail->issue;
				}
			}
			throw new Exception('ERROR updating payment (adding invoice number): '.$error_message);
		}
	}

	/**
	 * creates a payment to be used in conjunction with the paylink feature (ECM payment without line items)
	 * @param string $paycode a unique code used in the paylink
	 * @throws Exception if payment cannot be created
	 * @return PayPalPayment newly created payment
	 */
	public function createPaylinkPayment($paycode)
	{
		$orders_id                  = $paycode->orders_id;
		$amount                     = $paycode->amount;
		$paycode_hash               = $paycode->paycode;
		$order                      = new order((int)$orders_id);
		$intent                     = $this->config->get('intent');
		$mode                       = 'ecm';
		$paymentExperienceProfileId = $this->config->get('payment_experience_profile/' . $_SESSION['language_code']);
		
		$paymentArray = [
			'intent'        => $intent,
			'payer'         => [
				'payment_method' => 'paypal'
			],
			'transactions'  => [
				[
					'amount'      => [
						'currency' => $order->info['currency'],
						'total'    => $this->_formatAmount($amount, $order->info['currency']),
					],
					'description' => $this->encHelper->transcodeOutbound(STORE_NAME),
				],
			],
			'redirect_urls' => [
				'return_url' => str_replace('&amp;', '&', xtc_href_link('shop.php', 'do=PayPal/PaylinkReturn&code=' . $paycode_hash, 'SSL')),
				'cancel_url' => str_replace('&amp;', '&', xtc_href_link('index.php', '', 'SSL')),
			],
		];
		if(!empty($paymentExperienceProfileId))
		{
			$paymentArray['experience_profile_id'] = $paymentExperienceProfileId;
		}

		require_once DIR_FS_CATALOG.'gm/classes/JSON.php';
		$json = MainFactory::create('Services_JSON');
		$paymentJSON = $json->encodeUnsafe($paymentArray);

		$ppRestService = MainFactory::create_object('PayPalRestService');
		$ppRestRequest = MainFactory::create_object('PayPalRestRequest', array('POST', '/v1/payments/payment', $paymentJSON, 'ecm'));
		$response = $ppRestService->performRequest($ppRestRequest);
		$response_object = $response->getResponseObject();
		if($response_object === false)
		{
			throw new Exception('Error decoding response '.print_r($response, true));
		}
		if($response->getResponseCode() !== 201)
		{
			$error_message = $response_object->name.' '.$response_object->message;

			if(isset($response_object->details))
			{
				$error_message .= ', details: ';
				foreach($response_object->details as $detail)
				{
					$error_message .= $detail->field .': '.$detail->issue;
				}
			}
			throw new Exception('ERROR creating payment: '.$error_message);
		}

		$paypalPayment = MainFactory::create_object('PayPalPayment', array($response_object));
		return $paypalPayment;
	}
}
