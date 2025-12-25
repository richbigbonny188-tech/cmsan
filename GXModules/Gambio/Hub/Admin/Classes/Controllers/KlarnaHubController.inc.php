<?php
/* --------------------------------------------------------------
   KlarnaHubController.php 2022-08-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class KlarnaHubController
 *
 * Handles KlarnaHub specific operations that cannot be served by generic controllers.
 *
 * @category   System
 * @package    AdminHttpViewControllers
 * @extends    AdminHttpViewController
 */
class KlarnaHubController extends AdminHttpViewController
{
	/**
	 * @var \HubPublic\ValueObjects\HubClientKey
	 */
	protected $clientKey;
	
	/**
	 * @var KlarnaHubFactory
	 */
	protected $klarnaHubFactory;
	
	
	/**
	 * Initializes the controller class.
	 */
	public function init()
	{
		$this->klarnaHubFactory = MainFactory::create('KlarnaHubFactory');
		$clientKeyConfiguration = MainFactory::create('HubClientKeyConfiguration');
		
		try
		{
			$this->clientKey = $clientKeyConfiguration->getClientKey();
		}
		catch(Exception $exception)
		{
			// Continue without a client key.
		}
	}
	
	
	/**
	 * Returns KlarnaHub related configuration for use with JavaScript.
	 *
	 * Provide the "orderNumber" and "moduleCode" parameters to get more specific information on the current page.
	 *
	 * @return array|bool|\JsonHttpControllerResponse
	 */
	public function actionGetConfiguration()
	{
		try
		{
			$klarnaHubConfiguration = $this->klarnaHubFactory->createConfiguration();
			
			$orderNumberValue = $this->_getQueryParameter('orderNumber');
			$orderNumber      = $orderNumberValue !== null ? new NonEmptyStringType($orderNumberValue) : null;
			
			$moduleCodeValue = $this->_getQueryParameter('moduleCode');
			$moduleCode      = $moduleCodeValue !== null ? new NonEmptyStringType($moduleCodeValue) : null;
			
			$response = $klarnaHubConfiguration->asArray($this->clientKey, $moduleCode, $orderNumber);
		}
		catch(KlarnaHubException $exception)
		{
			$response = $exception->getErrorResponse();
		}
		catch(Exception $exception)
		{
			http_response_code(500);
			$response = AjaxException::response($exception);
		}
		
		return MainFactory::create('JsonHttpControllerResponse', $response);
	}
	
	
	/**
	 * Performs a full capture for a KlarnaHub order.
	 *
	 * @return JsonHttpControllerResponse
	 */
	public function actionExecuteFullCapture()
	{
		try
		{
			$languageTextManager = MainFactory::create('LanguageTextManager', 'gambio_hub_klarna_hub',
			                                           $_SESSION['languages_id']);
			
			$klarnaHubConfiguration = $this->klarnaHubFactory->createConfiguration();
			
			$orderNumberValue = $this->_getQueryParameter('orderNumber');
			$orderNumber      = $orderNumberValue !== null ? new NonEmptyStringType($orderNumberValue) : null;
			
			$moduleCodeValue = $this->_getQueryParameter('moduleCode');
			$moduleCode      = $moduleCodeValue !== null ? new NonEmptyStringType($moduleCodeValue) : null;
			
			$configuration = $klarnaHubConfiguration->asArray($this->clientKey, $moduleCode, $orderNumber);
			
			$order           = $configuration['order'];
			$customer        = $order['customer'];
			$billingAddress  = $order['addresses']['billing'];
			$shippingAddress = $order['addresses']['delivery'];
			
			$klarnaHubOrder = MainFactory::create('KlarnaHubOrder', StaticGXCoreLoader::getDatabaseQueryBuilder(),
			                                      $configuration['order'], $configuration['klarnaOrder']);
			
			$orderLines = $klarnaHubOrder->getUpdatedOrderLines();
			
			$billingAddressCountry = '';
			
			foreach($configuration['countries'] as $country)
			{
				if($country['countries_id'] == $billingAddress['countryId'])
				{
					$billingAddressCountry = $country['countries_name'];
					break;
				}
			}
			
			$shippingAddressCountry = '';
			
			foreach($configuration['countries'] as $country)
			{
				if($country['countries_id'] == $shippingAddress['countryId'])
				{
					$shippingAddressCountry = $country['countries_name'];
					break;
				}
			}
			
			$fullCaptureData = [
				'description'     => $languageTextManager->get_text('FULL_CAPTURE'),
				'capturedAt'      => date(DATETIME::ISO8601),
				'capturedAmount'  => $configuration['klarnaOrder']['order_amount'],
				'refundedAmount'  => 0,
				'orderLines'      => $orderLines,
				'billingAddress'  => [
					'givenName'      => $billingAddress['firstname'],
					'familyName'     => $billingAddress['lastname'],
					'title'          => $billingAddress[''],
					'streetAddress'  => $billingAddress['street']
					                    . (!empty($billingAddress['houseNumber']) ? $billingAddress['houseNumber'] : ''),
					'streetAddress2' => '',
					'postalCode'     => $billingAddress['postcode'],
					'city'           => $billingAddress['city'],
					'region'         => $billingAddress['suburb'],
					'country'        => $billingAddressCountry,
					'email'          => $customer['email'],
					'phone'          => $customer['phone']
				],
				'shippingAddress' => [
					'givenName'      => $shippingAddress['firstname'],
					'familyName'     => $shippingAddress['lastname'],
					'title'          => $shippingAddress[''],
					'streetAddress'  => $shippingAddress['street']
					                    . (!empty($shippingAddress['houseNumber']) ? $shippingAddress['houseNumber'] : ''),
					'streetAddress2' => '',
					'postalCode'     => $shippingAddress['postcode'],
					'city'           => $shippingAddress['city'],
					'region'         => $shippingAddress['suburb'],
					'country'        => $shippingAddressCountry,
					'email'          => $customer['email'],
					'phone'          => $customer['phone']
				],
				'shippingInfo'    => []
			];
			
			$this->_createKlarnaHubCallbackClient()->executeFullCapture($fullCaptureData);
			
			$response = ['success' => true];
		}
		catch(KlarnaHubException $exception)
		{
			$response = $exception->getErrorResponse();
		}
		catch(Exception $exception)
		{
			http_response_code(500);
			$response = AjaxException::response($exception);
		}
		
		return MainFactory::create('JsonHttpControllerResponse', $response);
	}
	
	
	/**
	 * Performs a capture for a KlarnaHub order.
	 *
	 * @return JsonHttpControllerResponse
	 */
	public function actionExecuteCapture()
	{
		try
		{
			$selectedOrderLines = $this->_getParsedBody();
			
			if(empty($selectedOrderLines))
			{
				$response = ['success' => true, 'orderLines' => $selectedOrderLines];
				
				return MainFactory::create('JsonHttpControllerResponse', $response);
			}
			
			$languageTextManager = MainFactory::create('LanguageTextManager', 'gambio_hub_klarna_hub',
			                                           $_SESSION['languages_id']);
			
			$klarnaHubConfiguration = $this->klarnaHubFactory->createConfiguration();
			
			$orderNumberValue = $this->_getQueryParameter('orderNumber');
			$orderNumber      = $orderNumberValue !== null ? new NonEmptyStringType($orderNumberValue) : null;
			
			$moduleCodeValue = $this->_getQueryParameter('moduleCode');
			$moduleCode      = $moduleCodeValue !== null ? new NonEmptyStringType($moduleCodeValue) : null;
			
			$configuration = $klarnaHubConfiguration->asArray($this->clientKey, $moduleCode, $orderNumber);
			
			$order           = $configuration['order'];
			$customer        = $order['customer'];
			$billingAddress  = $order['addresses']['billing'];
			$shippingAddress = $order['addresses']['delivery'];
			
			$klarnaHubOrder = MainFactory::create('KlarnaHubOrder', StaticGXCoreLoader::getDatabaseQueryBuilder(),
			                                      $configuration['order'], $configuration['klarnaOrder']);
			
			$orderLines = $klarnaHubOrder->getUpdatedOrderLines();
			
			// Check if surcharges have already been captured.
            $surchargeOrderLines = [];
            $surchargesCaptured = false;
            
            foreach($orderLines as $orderLine) {
                if ($orderLine['type'] !== 'surcharge') {
                    continue;
                }
                
                $surchageOrderLines[] = $orderLine;
            }
            
            foreach($configuration['klarnaOrder']['captures'] as $capture) {
                foreach($capture['order_lines'] as $capturedOrderLine) {
                    if ($capturedOrderLine['type'] === 'surcharge') {
                        $surchargesCaptured = true;
                        break 2;
                    }
                }
            }
			
			// Check if a voucher exists and if it's already captured.
			$voucherOrderLine = null;
			$voucherCaptured = false;
			
			foreach($orderLines as $orderLine) {
				if ($orderLine['reference'] !== 'ot_coupon') {
					continue;
				}
				
				$voucherOrderLine = $orderLine;
				
				foreach($configuration['klarnaOrder']['captures'] as $capture) {
					foreach($capture['order_lines'] as $capturedOrderLine) {
						if ($capturedOrderLine['reference'] === 'ot_coupon') {
							$voucherCaptured = true;
							break 2;
						}
					}
				}
			}
   
			$isVoucherSelected = false;
            foreach($selectedOrderLines as $selectedOrderLine) {
                if ($selectedOrderLine['reference'] === 'ot_coupon') {
                    $isVoucherSelected = true;
                    break;
                }
            }
            
            // Check if a discount exists and if it's already captured.
            $discountOrderLine = null;
            $discountCaptured = false;
            
            foreach($orderLines as $orderLine) {
                if ($orderLine['reference'] !== 'ot_discount') {
                    continue;
                }
                
                $discountOrderLine = $orderLine;
                
                foreach($configuration['klarnaOrder']['captures'] as $capture) {
                    foreach($capture['order_lines'] as $capturedOrderLine) {
                        if ($capturedOrderLine['reference'] === 'ot_discount') {
                            $discountCaptured = true;
                            break 2;
                        }
                    }
                }
            }
            
            $isDiscountSelected = false;
            foreach($selectedOrderLines as $selectedOrderLine) {
                if ($selectedOrderLine['reference'] === 'ot_discount') {
                    $isDiscountSelected = true;
                    break;
                }
            }
            
            // Check if a gift exists and if it's already captured.
            $giftCodeOrderLine = null;
            $giftCodeCaptured = false;
            
            foreach($orderLines as $orderLine) {
                if ($orderLine['reference'] !== 'ot_gv') {
                    continue;
                }
                
                $giftCodeOrderLine = $orderLine;
                
                foreach($configuration['klarnaOrder']['captures'] as $capture) {
                    foreach($capture['order_lines'] as $capturedOrderLine) {
                        if ($capturedOrderLine['reference'] === 'ot_gv') {
                            $giftCodeCaptured = true;
                            break 2;
                        }
                    }
                }
            }
            
            $isGiftCodeSelected = false;
            foreach($selectedOrderLines as $selectedOrderLine) {
                if ($selectedOrderLine['reference'] === 'ot_gv') {
                    $isGiftCodeSelected = true;
                    break;
                }
            }
			
			// Filter order lines with the provided ones.
			$capturedOrderLines = array_filter($orderLines, function ($orderLine) use ($selectedOrderLines) {
				$found = false;
				
				foreach($selectedOrderLines as $selectedOrderLine)
				{
					if($selectedOrderLine['reference'] === $orderLine['reference'])
					{
						$found = true;
						break;
					}
				}
				
				return $found;
			});
			
			if(empty($capturedOrderLines))
			{
				throw new \RuntimeException('No order lines matched for capture.');
			}
			
			if (!empty($voucherOrderLine) && !$voucherCaptured && !$isVoucherSelected) {
				$capturedOrderLines[] = $voucherOrderLine;
			}
            
            if (!empty($discountOrderLine) && !$discountCaptured && !$isDiscountSelected) {
                $capturedOrderLines[] = $discountOrderLine;
            }
            
            if (!empty($giftCodeOrderLine) && !$giftCodeCaptured && !$isGiftCodeSelected) {
                $capturedOrderLines[] = $giftCodeOrderLine;
            }
            
            if (!empty($surchargeOrderLines) && !$surchargesCaptured) {
                $capturedOrderLines = array_merge($capturedOrderLines, $surchargeOrderLines);
            }
			
			$billingAddressCountry = '';
			
			foreach($configuration['countries'] as $country)
			{
				if($country['countries_id'] == $billingAddress['countryId'])
				{
					$billingAddressCountry = $country['countries_name'];
					break;
				}
			}
			
			$shippingAddressCountry = '';
			
			foreach($configuration['countries'] as $country)
			{
				if($country['countries_id'] == $shippingAddress['countryId'])
				{
					$shippingAddressCountry = $country['countries_name'];
					break;
				}
			}
			
			$capturedAmount = 0;
			
			foreach($capturedOrderLines as $capturedOrderLine)
			{
				$capturedAmount += (int)$capturedOrderLine['totalAmount'];
			}
			
			$captureData = [
				'description'     => $languageTextManager->get_text('CAPTURE'),
				'capturedAt'      => date(DATETIME::ISO8601),
				'capturedAmount'  => $capturedAmount,
				'refundedAmount'  => 0,
				'orderLines'      => $capturedOrderLines,
				'billingAddress'  => [
					'givenName'      => $billingAddress['firstname'],
					'familyName'     => $billingAddress['lastname'],
					'title'          => $billingAddress[''] ?? null,
					'streetAddress'  => $billingAddress['street']
					                    . (!empty($billingAddress['houseNumber']) ? $billingAddress['houseNumber'] : ''),
					'streetAddress2' => '',
					'postalCode'     => $billingAddress['postcode'],
					'city'           => $billingAddress['city'],
					'region'         => $billingAddress['suburb'],
					'country'        => $billingAddressCountry,
					'email'          => $customer['email'],
					'phone'          => $customer['phone']
				],
				'shippingAddress' => [
					'givenName'      => $shippingAddress['firstname'],
					'familyName'     => $shippingAddress['lastname'],
					'title'          => $shippingAddress[''] ?? null,
					'streetAddress'  => $shippingAddress['street']
					                    . (!empty($shippingAddress['houseNumber']) ? $shippingAddress['houseNumber'] : ''),
					'streetAddress2' => '',
					'postalCode'     => $shippingAddress['postcode'],
					'city'           => $shippingAddress['city'],
					'region'         => $shippingAddress['suburb'],
					'country'        => $shippingAddressCountry,
					'email'          => $customer['email'],
					'phone'          => $customer['phone']
				],
				'shippingInfo'    => []
			];
			
			$this->_createKlarnaHubCallbackClient()->executeCapture($captureData);
			
			$response = ['success' => true];
		}
		catch(KlarnaHubException $exception)
		{
			$response = $exception->getErrorResponse();
		}
		catch(Exception $exception)
		{
			http_response_code(500);
			$response = AjaxException::response($exception);
		}
		
		return MainFactory::create('JsonHttpControllerResponse', $response);
	}
	
	
	/**
	 * Adds a tracking code to the last capture of a KlarnaHub order.
	 *
	 * @return JsonHttpControllerResponse
	 */
	public function actionExecuteAddTrackingCode()
	{
		try
		{
			$parsedBody = $this->_getParsedBody();
			
			$addTrackingCodeData = [
				'shippingCompany'       => $parsedBody['shippingCompany'],
				'shippingMethod'        => 'Own',
				'trackingNumber'        => $parsedBody['trackingNumber'],
				'trackingUri'           => null,
				'returnShippingCompany' => null,
				'returnTrackingNumber'  => null,
				'returnTrackingUri'     => null
			];
			
			// Fetch tracking URL directly from the database (not always possible in frontend).
			$trackingCode = StaticGXCoreLoader::getDatabaseQueryBuilder()->get_where('orders_parcel_tracking_codes', [
				'order_id'      => $this->_getQueryParameter('orderNumber'),
				'tracking_code' => $addTrackingCodeData['trackingNumber']
			])->row_array();
			
			if($trackingCode)
			{
				$addTrackingCodeData['trackingUri'] = $trackingCode['url'];
			}
			
			$this->_createKlarnaHubCallbackClient()->executeAddTrackingCode($addTrackingCodeData);
			
			$response = ['success' => true];
		}
		catch(KlarnaHubException $exception)
		{
			http_response_code(500);
			$response = $exception->getErrorResponse();
		}
		catch(Exception $exception)
		{
			$response = AjaxException::response($exception);
		}
		
		return MainFactory::create('JsonHttpControllerResponse', $response);
	}
	
	
	/**
	 * Cancels a KlarnaHub order.
	 *
	 * @return JsonHttpControllerResponse
	 */
	public function actionExecuteCancelOrder()
	{
		try
		{
			$this->_createKlarnaHubCallbackClient()->executeCancelOrder();
			
			$response = ['success' => true];
		}
		catch(KlarnaHubException $exception)
		{
			$response = $exception->getErrorResponse();
		}
		catch(Exception $exception)
		{
			http_response_code(500);
			$response = AjaxException::response($exception);
		}
		
		return MainFactory::create('JsonHttpControllerResponse', $response);
	}
	
	
	/**
	 * Activates the recalculation flag for the provided order.
	 *
	 * @return JsonHttpControllerResponse
	 */
	public function actionActivateRecalculationFlag()
	{
		try
		{
			$parsedBody = $this->_getParsedBody();
			
			$key = 'GAMBIO_HUB_KLARNA_HUB_RECALCULATE';
			
			$recalculate = json_decode((string)gm_get_conf($key), true);
			
			$orderNumber = $this->_getQueryParameter('orderNumber');
			
			$recalculate[$orderNumber] = $parsedBody;
			
			gm_set_conf($key, json_encode($recalculate));
			
			$response = ['success' => true];
		}
		catch(Exception $exception)
		{
			http_response_code(500);
			$response = AjaxException::response($exception);
		}
		
		return MainFactory::create('JsonHttpControllerResponse', $response);
	}
	
	
	/**
	 * Returns the parsed body contents of the request (JSON decoded array).
	 *
	 * @return array
	 */
	protected function _getParsedBody()
	{
		return json_decode(file_get_contents('php://input'), true);
	}
	
	
	/**
	 * Creates a KlarnaHubCallbackClient instance.
	 *
	 * @return KlarnaHubCallbackClient
	 */
	protected function _createKlarnaHubCallbackClient()
	{
		$moduleCodeValue = $this->_getQueryParameter('moduleCode');
		
		if($moduleCodeValue === null)
		{
			throw new InvalidArgumentException('Module code GET parameter was not provided.');
		}
		
		$moduleCode = new NonEmptyStringType($moduleCodeValue);
		
		$orderNumberValue = $this->_getQueryParameter('orderNumber');
		
		if($orderNumberValue === null)
		{
			throw new InvalidArgumentException('Order number GET parameter was not provided.');
		}
		
		$orderNumber = new NonEmptyStringType($orderNumberValue);
		
		return $this->klarnaHubFactory->createCallbackClient($moduleCode, $orderNumber);
	}
}
