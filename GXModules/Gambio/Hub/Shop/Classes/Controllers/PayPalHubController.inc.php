<?php
/* --------------------------------------------------------------
   PayPalHubController.inc.php 2023-01-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use \HubPublic\Http\CurlRequest;

class PayPalHubController extends HttpViewController
{
    public function init()
    {

    }
    
    /**
     * @param \HttpContextInterface $httpContext
     */
    public function proceed(HttpContextInterface $httpContext)
    {
        $this->serverDataArray = $this->httpContextReader->getServerData($httpContext);
        
        parent::proceed($httpContext);
    }
    
    
    public function actionAddToCart()
    {
        $productsId         = (int)$this->_getQueryParameter('products_id');
        $productsQty        = $this->_getQueryParameter('qty');
        $cartActionsProcess = MainFactory::create('CartActionsProcess');
        $cartActionsProcess->set_('coo_cart', $_SESSION['cart']);
        if (isset($GLOBALS['order']) && $GLOBALS['order'] !== null) {
            $cartActionsProcess->set_('coo_order', $GLOBALS['order']);
        }
        $cartActionsProcess->set_('coo_price', $GLOBALS['xtPrice']);
        if (isset($_SESSION['customer_id'])) {
            $cartActionsProcess->set_('customer_id', $_SESSION['customer_id']);
        }
        $cartActionsProcess->set_('customers_status_id', $_SESSION['customers_status']['customers_status_id']);
        if (isset($_SESSION['customers_status']['customers_fsk18_purchasable'])) {
            $cartActionsProcess->set_('customers_fsk18_purchasable', $_SESSION['customers_status']['customers_fsk18_purchasable']);
        } else {
            $cartActionsProcess->set_('customers_fsk18', $_SESSION['customers_status']['customers_fsk18']);
        }
        $cartActionsProcess->set_('customers_fsk18_display', $_SESSION['customers_status']['customers_fsk18_display']);
        $cartActionsProcess->set_data(
            'GET',
            [
                'BUYproducts_id' => $productsId,
            ]
        );
        $cartActionsProcess->set_data(
            'POST',
            [
                'products_qty' => $productsQty,
            ]
        );
        $cartActionsProcess->proceed('buy_now');
        
        return MainFactory::create('RedirectHttpControllerResponse', 'shopping_cart.php?display_mode=ecs');
    }
    

    public function actionCreatePayment()
    {
        $payment = $this->makePayPalHubPayment();
        $payment = $payment['payment'];
        
        $response = [
            'payment' => $payment,
        ];
        return MainFactory::create('JsonHttpControllerResponse', $response);
    }
    
    public function actionCreateOrder()
    {
        $order = $this->makePayPalHubOrder();
        return MainFactory::create('JsonHttpControllerResponse', $order);
    }
    
    public function actionApprovedOrder()
    {
        $initiator = (string)$this->_getQueryParameter('initiator') === 'ecs' ? 'ecs' : 'ecm';
        $postData  = json_decode(file_get_contents('php://input'), true);
    
        $_SESSION['PayPal2Hub'] = [
            'orderID'   => isset($postData['orderID']) ? $postData['orderID'] : null,
            'payerID'   => isset($postData['payerID']) ? $postData['payerID'] : null,
            'initiator' => $initiator,
        ];
        $_SESSION['payment']              = 'gambio_hub';
        $_SESSION['gambio_hub_selection'] = 'PayPal2Hub';
        $response                         = [
            'status' => 'OK',
        ];

        return MainFactory::create('JsonHttpControllerResponse', $response);
    }
    
    
    public function actionAuthorizedPayment()
    {
        $paymentID                        = $this->_getPostData('paymentID');
        $payerID                          = $this->_getPostData('payerID');
        $initiator                        = (string)$this->_getQueryParameter('initiator') === 'ecs' ? 'ecs' : 'ecm';
        $_SESSION['PayPal2Hub']           = [
            'paymentID' => $paymentID,
            'payerID'   => $payerID,
            'initiator' => $initiator,
        ];
        $_SESSION['payment']              = 'gambio_hub';
        $_SESSION['gambio_hub_selection'] = 'PayPal2Hub';
        $response                         = [
            'status' => 'OK',
        ];
        
        return MainFactory::create('JsonHttpControllerResponse', $response);
    }
    
    public function actionRedirectGuest()
    {
        if(isset($_SESSION['PayPal2Hub']['paymentID'], $_SESSION['PayPal2Hub']['payerID']))
        {
            $payment = $this->getPayPalPayment($_SESSION['PayPal2Hub']['paymentID']);
            $payerEmailAddress = $payment->payer->payer_info->email;
            $customerId = $this->findCustomerByEmail($payerEmailAddress);
            if($customerId !== false)
            {
                // known customer, log in
                $loginContentControl = MainFactory::create('LoginContentControl');
                $loginContentControl->loginAfterSuccessfulAuthorization($customerId);
                $redirectUrl = xtc_href_link('checkout_shipping.php', '', 'SSL', false, false, false, false, false);
            }
            else
            {
                // new customer, gather data and redirect to account registration
                $countryId = STORE_COUNTRY;
                if(!empty($payment->payer->payer_info->country_code))
                {
                    $countryService = StaticGXCoreLoader::getService('Country');
                    /** @var \CustomerCountry $country */
                    $country        = $countryService->getCountryByIso2($payment->payer->payer_info->country_code);
                    $countryId      = $country->getId();
                }

                $_SESSION['paypalCustomerCollection'] = [
                    'firstname'              => $payment->payer->payer_info->first_name,
                    'lastname'               => $payment->payer->payer_info->last_name,
                    'email_address'          => $payment->payer->payer_info->email,
                    'email_address_confirm'  => $payment->payer->payer_info->email,
                    'country'                => $countryId,
                    'street_address'         => $payment->payer->payer_info->shipping_address->line1,
                    'city'                   => $payment->payer->payer_info->shipping_address->city,
                    'postcode'               => $payment->payer->payer_info->shipping_address->postal_code,
                    'telephone'              => $payment->payer->payer_info->shipping_address->phone,
                ];
                $redirectUrl = xtc_href_link('shop.php', 'do=CreateRegistree&checkout_started=1', 'SSL', false, false, false, false, false);
            }
        }
        elseif(isset($_SESSION['PayPal2Hub']['orderID'], $_SESSION['PayPal2Hub']['payerID']))
        {
            $customerData = [
                'firstname'      => '',
                'lastname'       => '',
                'email_address'  => '',
                'country_code'   => '',
                'street_address' => '',
                'city'           => '',
                'postcode'       => '',
                'telephone'      => '',
            ];
    
            // retrieve customer data from hub
            $query = [
                'client_key' => gm_get_conf('GAMBIO_HUB_CLIENT_KEY'),
                'order_id'   => $_SESSION['PayPal2Hub']['orderID'],
            ];
            /** @var HubSettings $hubSettings */
            $hubSettings = MainFactory::create('HubSettings', gm_get_conf('GAMBIO_HUB_CURL_TIMEOUT'));
    
            /** @var \HubCallbackApiClient $hubCallbackApiClient */
            $hubCallbackApiClient = MainFactory::create('HubCallbackApiClient', MODULE_PAYMENT_GAMBIO_HUB_URL,
                new CurlRequest(), LogControl::get_instance(), $hubSettings);
            try
            {
                /** @var \HttpResponse $response */
                $response = $hubCallbackApiClient->execute('PayPal2Hub', true, ['source' => 'retrieve_customer_data'], $query);
        
                if($response->getStatusCode() !== 200)
                {
                    throw new RuntimeException('Error retrieving customer data from hub');
                }
        
                $customerData = json_decode($response->getBody(), true);
                $customerId = $this->findCustomerByEmail($customerData['email']);
                if($customerId !== false)
                {
                    // known customer, log in
                    $loginContentControl = MainFactory::create('LoginContentControl');
                    $loginContentControl->loginAfterSuccessfulAuthorization($customerId);
                    $redirectUrl = xtc_href_link('checkout_shipping.php', '', 'SSL', false, false, false, false, false);
                }
                else {
                    // new customer, gather data and redirect to account registration
                    $countryId = STORE_COUNTRY;
                    if(!empty($customerData['country_code']))
                    {
                        $countryService = StaticGXCoreLoader::getService('Country');
                        /** @var \CustomerCountry $country */
                        $country        = $countryService->getCountryByIso2($customerData['country_code']);
                        $countryId      = $country->getId();
                    }
                    $street      = $customerData['street_address'];
                    $houseNumber = '';
                    if (defined('ACCOUNT_SPLIT_STREET_INFORMATION') && ACCOUNT_SPLIT_STREET_INFORMATION === 'true') {
                        $splitStreet = $this->splitStreet($street);
                        $street      = $splitStreet['street'];
                        $houseNumber = $splitStreet['house_no'];
                    }
    
                    $_SESSION['paypalCustomerCollection'] = [
                        'firstname'             => $customerData['firstname'],
                        'lastname'              => $customerData['lastname'],
                        'email_address'         => $customerData['email'],
                        'email_address_confirm' => $customerData['email'],
                        'country'               => $countryId,
                        'street_address'        => $street,
                        'house_number'          => $houseNumber,
                        'city'                  => $customerData['city'],
                        'postcode'              => $customerData['postcode'],
                        'telephone'             => $customerData['telephone'],
                    ];
                    $redirectUrl = xtc_href_link('shop.php', 'do=CreateRegistree&checkout_started=1', 'SSL', false, false, false, false, false);
                }
            }
            catch(Exception $exception)
            {
                $redirectUrl = xtc_href_link('shopping_cart.php', 'error=user_data', 'SSL', false, false, false, false, false);
            }
        }
        else
        {
            throw new \RuntimeException('unauthorized');
        }
        
        return new RedirectHttpControllerResponse($redirectUrl);
    }
    
    /**
     * Finds customer by e-mail address.
     *
     * @param $emailAddress
     *
     * @return bool
     */
    protected function findCustomerByEmail($emailAddress)
    {
        $customerService = StaticGXCoreLoader::getService('Customer');
        $customers       = $customerService->filterCustomers(['customers_email_address' => $emailAddress]);
        $customerId      = false;
        
        /** @var Customer $customer */
        foreach ($customers as $customer) {
            if ($customer->isGuest() === false) {
                $customerId = $customer->getId();
                break;
            }
        }
        
        return $customerId;
    }
    
    protected function getPayPalPayment($paymentID)
    {
        $ppRestService = MainFactory::create('PayPalRestService');
        $ppRestRequest = MainFactory::create('PayPalRestRequest', 'GET', '/v1/payments/payment/' . $paymentID, null, 'hub');
        $response = $ppRestService->performRequest($ppRestRequest);
        $responseObject = $response->getResponseObject();
        if($responseObject === false)
        {
            $text = MainFactory::create('PayPalText');
            throw new Exception($text->get_text('error_decoding_response'));
        }
        if(!in_array((int)$response->getResponseCode(), [200, 201], true))
        {
            throw new Exception('Error retrieving payment \'' . $paymentID . '\'');
        }
        $payment = MainFactory::create('PayPalPayment', $responseObject);
        return $payment;
    }
    
    protected function makePayPalHubPayment()
    {
        require_once DIR_FS_CATALOG . 'includes/classes/order.php';
        /** @var \order_ORIGIN $order */
        $order = new order();
        $GLOBALS['order'] = $order;
        /** @var \order_total_ORIGIN $order_total_modules */
        $order_total_modules = new order_total();
        $order_total_modules->collect_posts();
        $order_total_modules->pre_confirmation_check();
        $totals                 = $order_total_modules->process();
        $amount = $order->info['total'];
        $initiator = (string)$this->_getQueryParameter('initiator') === 'ecs' ? 'ecs' : 'ecm';
        $hubPayPalConfiguration = MainFactory::create('HubPayPalConfiguration');
        $brandName = $hubPayPalConfiguration->getConfigBrandName();
    
        $paypalLocaleFactory = MainFactory::create('HubPayPalLocaleFactory');
        
        $query = [
            'client_key'   => gm_get_conf('GAMBIO_HUB_CLIENT_KEY'),
            'devmode'      => file_exists(DIR_FS_CATALOG . '.dev-environment') ? 'true' : 'false',
            'intent'       => 'sale',
            'totalAmount'  => number_format((float)$amount, 2, '.', ''),
            'currencyCode' => $_SESSION['currency'],
            'returnUrl'    => 'https://www.paypal.com/checkoutnow/error',
            'cancelUrl'    => 'https://www.paypal.com/checkoutnow/error',
            'localeCode'   => $paypalLocaleFactory->getLocaleByLanguageAndCountry($_SESSION['language_code'],$_SESSION['delivery_zone']),
            'brandName'    => $brandName,
            'initiator'    => $initiator,
            'addressMode'  => isset($_SESSION['customer_id']) ? 'NO_SHIPPING' : 'GET_FROM_FILE',
        ];

        if (!empty($_SESSION['sendto'])) {
            /** @var \AddressBookService $addressBookService */
            $addressBookService = StaticGXCoreLoader::getService('AddressBook');
            $sendtoAddress = $addressBookService->findAddressById(new IdType((int)$_SESSION['sendto']));
            if ($sendtoAddress !== null && isset($_SESSION['customer_id'])
                && $sendtoAddress->getCustomerId() === (int)$_SESSION['customer_id']) {
                $query['shippingAddressRecipientName'] = (string)$sendtoAddress->getFirstname() . ' '
                                                         . (string)$sendtoAddress->getLastname();
                $query['shippingAddressLine1']         = (string)$sendtoAddress->getStreet() . rtrim(
                        ' ' . (string)$sendtoAddress->getHouseNumber()
                    );
                $query['shippingAddressLine2']         = (string)$sendtoAddress->getAdditionalAddressInfo();
                $query['shippingAddressCity']          = (string)$sendtoAddress->getCity();
                $query['shippingAddressCountryCode']   = (string)$sendtoAddress->getCountry()->getIso2();
                $query['shippingAddressPostalCode']    = (string)$sendtoAddress->getPostcode();
                $query['shippingAddressState']         = (string)$sendtoAddress->getCountryZone()->getCode();
                $query['shippingAddressPhone']         = '';
            }
        }

        /** @var HubSettings $hubSettings */
        $hubSettings = MainFactory::create('HubSettings', gm_get_conf('GAMBIO_HUB_CURL_TIMEOUT'));
    
        /** @var \HubCallbackApiClient $hubCallbackApiClient */
        $hubCallbackApiClient = MainFactory::create('HubCallbackApiClient', MODULE_PAYMENT_GAMBIO_HUB_URL,
                                                    new CurlRequest(), LogControl::get_instance(), $hubSettings);
        try
        {
            /** @var \HttpResponse $response */
            $response = $hubCallbackApiClient->execute('PayPal2Hub', true, ['source' => 'create_payment'], $query);
        
            if($response->getStatusCode() !== 200)
            {
                throw new RuntimeException('Error creating payment on hub');
            }
        
            $responseBody = json_decode($response->getBody(), true);
        }
        catch(Exception $exception)
        {
            $responseBody = [
                'error' => 'could not create payment: ' . $exception->getMessage(),
            ];
        }
    
        return $responseBody;
    }
    
    
    protected function makePayPalHubOrder()
    {
        require_once DIR_FS_CATALOG . 'includes/classes/order.php';
        /** @var \order_ORIGIN $order */
        $order = new order();
        $GLOBALS['order'] = $order;
        $sessionPayment = $_SESSION['payment'];
        $_SESSION['payment'] = 'PayPal2Hub';
        /** @var \order_total_ORIGIN $order_total_modules */
        $order_total_modules = new order_total();
        $order_total_modules->collect_posts();
        $order_total_modules->pre_confirmation_check();
        $totals                 = $order_total_modules->process();
        $amount = $order->info['total'];
        $_SESSION['payment'] = $sessionPayment;
        
        foreach ($totals as $total) {
            if ($total['code'] === 'ot_total') {
                $amount = $total['value'];
            }
        }
        
        $initiator = (string)$this->_getQueryParameter('initiator') === 'ecs' ? 'ecs' : 'ecm';
        $hubPayPalConfiguration = MainFactory::create('HubPayPalConfiguration');
        $brandName = $hubPayPalConfiguration->getConfigBrandName();
        
        $paypalLocaleFactory = MainFactory::create('HubPayPalLocaleFactory');
        
        $query = [
            'client_key'   => gm_get_conf('GAMBIO_HUB_CLIENT_KEY'),
            'devmode'      => file_exists(DIR_FS_CATALOG . '.dev-environment') ? 'true' : 'false',
            'intent'       => 'sale',
            'totalAmount'  => number_format((float)$amount, 2, '.', ''),
            'currencyCode' => $_SESSION['currency'],
            'returnUrl'    => 'https://www.paypal.com/checkoutnow/error',
            'cancelUrl'    => 'https://www.paypal.com/checkoutnow/error',
            'localeCode'   => $paypalLocaleFactory->getLocaleByLanguageAndCountry($_SESSION['language_code'],$_SESSION['delivery_zone']),
            'brandName'    => $brandName,
            'initiator'    => $initiator,
            'addressMode'  => isset($_SESSION['customer_id']) ? 'NO_SHIPPING' : 'GET_FROM_FILE',
        ];
        
        if (!empty($_SESSION['sendto'])) {
            /** @var \AddressBookService $addressBookService */
            $addressBookService = StaticGXCoreLoader::getService('AddressBook');
            $sendtoAddress = $addressBookService->findAddressById(new IdType((int)$_SESSION['sendto']));
            if ($sendtoAddress !== null && isset($_SESSION['customer_id'])
                && $sendtoAddress->getCustomerId() === (int)$_SESSION['customer_id']) {
                $query['shippingAddressRecipientName'] = (string)$sendtoAddress->getFirstname() . ' '
                                                         . (string)$sendtoAddress->getLastname();
                $query['shippingAddressLine1']         = (string)$sendtoAddress->getStreet() . rtrim(
                        ' ' . (string)$sendtoAddress->getHouseNumber()
                    );
                $query['shippingAddressLine2']         = (string)$sendtoAddress->getAdditionalAddressInfo();
                $query['shippingAddressCity']          = (string)$sendtoAddress->getCity();
                $query['shippingAddressCountryCode']   = (string)$sendtoAddress->getCountry()->getIso2();
                $query['shippingAddressPostalCode']    = (string)$sendtoAddress->getPostcode();
                $query['shippingAddressState']         = (string)$sendtoAddress->getCountryZone()->getCode();
                $query['shippingAddressPhone']         = '';
            }
        }
        
        /** @var HubSettings $hubSettings */
        $hubSettings = MainFactory::create('HubSettings', gm_get_conf('GAMBIO_HUB_CURL_TIMEOUT'));
        
        /** @var \HubCallbackApiClient $hubCallbackApiClient */
        $hubCallbackApiClient = MainFactory::create('HubCallbackApiClient', MODULE_PAYMENT_GAMBIO_HUB_URL,
            new CurlRequest(), LogControl::get_instance(), $hubSettings);
        try
        {
            /** @var \HttpResponse $response */
            $response = $hubCallbackApiClient->execute('PayPal2Hub', true, ['source' => 'create_order'], $query);
            
            if($response->getStatusCode() !== 200)
            {
                throw new RuntimeException('Error creating payment on hub');
            }
            
            $responseBody = json_decode($response->getBody(), true);
        }
        catch(Exception $exception)
        {
            $responseBody = [
                'error' => 'could not create payment: ' . $exception->getMessage(),
            ];
        }
        
        return $responseBody;
    }
    
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
