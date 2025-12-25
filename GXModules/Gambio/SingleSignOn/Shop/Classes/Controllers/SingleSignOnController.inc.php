<?php
/* --------------------------------------------------------------
   SingleSignOnController.inc.php 2023-04-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class SingleSignOnController extends HttpViewController
{
    /**
     * Mapping of symbolic service names to issuer keys.
     * @var array
     */
    protected $validServices = [
        'google'   => 'https://accounts.google.com',
        'paypal'   => 'paypal.com',
        'facebook' => 'facebook.com',
        'amazon'   => 'amazon.com'
    ];
    
    
    public function init()
    {
        $moduleInstalled = (bool)gm_get_conf('MODULE_CENTER_SINGLESIGNON_INSTALLED');
        if ($moduleInstalled === false) {
            throw new Exception('Single Sign-on module not installed');
        }
    }
    
    
    public function proceed(HttpContextInterface $context)
    {
        return parent::proceed($context);
    }
    
    
    /**
     * Default action redirects to login page.
     *
     * @return array|bool|RedirectHttpControllerResponse
     */
    public function actionDefault()
    {
        $loginUrl = xtc_href_link('login.php', '', 'SSL', false, false, false, false, false);
        
        return MainFactory::create('RedirectHttpControllerResponse', $loginUrl);
    }
    
    
    /**
     * Redirect action redirects to SSO service.
     *
     * @return array|bool
     * @throws InvalidSignonServiceException
     */
    public function actionRedirect()
    {
        $service = $this->_getQueryParameter('service');
        if (isset($_SESSION['customer_id']) && $service !== 'amazon'
            && $_SESSION['customers_status']['customers_status_id'] === DEFAULT_CUSTOMERS_STATUS_ID_GUEST) {
            throw new RuntimeException('Single Sign-On is not available for guest accounts.');
        }
        $checkoutStarted = (bool)$this->_getQueryParameter('checkout_started');
        $returnUrl       = $this->_getQueryParameter('return_url');
        $returnUrlHash   = $this->_getQueryParameter('return_url_hash');
        if (!empty($returnUrl) && !empty($returnUrlHash)) {
            if ($returnUrlHash === hash('sha256', $returnUrl . LogControl::get_secure_token())) {
                $_SESSION['sso_redirect_after_login'] = $returnUrl;
            } else {
                unset($_SESSION['sso_redirect_after_login']);
            }
        }
        
        $_SESSION['sso_checkout_started'] = $checkoutStarted;
        if (!empty($service)) {
            $ssoService  = $this->createSingleSignonService($service);
            $redirectUrl = $ssoService->getAuthorizationLink();
            if (!empty($redirectUrl)) {
                $response = MainFactory::create('RedirectHttpControllerResponse', $redirectUrl);
            } else {
                $redirectPageContent = $ssoService->getAuthorizationLinkPage();
                $response            = MainFactory::create('HttpControllerResponse', $redirectPageContent);
            }
        } else {
            throw new InvalidSignonServiceException();
        }
        
        return $response;
    }
    
    
    /**
     * Login action (SSO redirect target) processes authorization code and redirects accordingly.
     *
     * @return array|bool|RedirectHttpControllerResponse
     * @throws Exception
     */
    public function actionLogin()
    {
        $code    = $this->_getQueryParameter('code');
        $service = $this->_getQueryParameter('service');
        if (!empty($code) && !empty($service)) {
            $ssoService          = $this->createSingleSignonService($service);
            $_SESSION['ssoData'] = $ssoService->processAuthorizationCode($code);
            if (empty($_SESSION['customer_id'])) {
                // customer was not logged in before SSO was triggered
                $customerId = $this->findCustomerByIssuerAndSubject($_SESSION['ssoData']['iss'],
                                                                    $_SESSION['ssoData']['sub']);
                if ($customerId === false) {
                    // customer has not used SSO before
                    if (!empty($_SESSION['ssoData']['customer_collection']['email_address'])
                        && (bool)$_SESSION['ssoData']['customer_collection']['email_address_verified'] === true) {
                        // SSO service provided a trusted email address
                        $customerId = $this->findCustomerByEmail($_SESSION['ssoData']['customer_collection']['email_address']);
                        if (false !== $customerId) {
                            $this->storeSSOData($customerId, $_SESSION['ssoData']['iss'], $_SESSION['ssoData']['sub']);
                        }
                    }
                }
                if ($customerId !== false) {
                    // identity supplied by SSO service matches an existing customer -> log in
                    $loginContentControl = MainFactory::create('LoginContentControl');
                    $loginContentControl->loginAfterSuccessfulAuthorization($customerId);
                }
            } else {
                // customer was logged in when SSO was triggered, store SSO connection if they were not logged in as guest
                if ($_SESSION['customers_status']['customers_status_id'] !== DEFAULT_CUSTOMERS_STATUS_ID_GUEST) {
                    $this->storeSSOData($_SESSION['customer_id'],
                                        $_SESSION['ssoData']['iss'],
                                        $_SESSION['ssoData']['sub']);
                }
            }
        }
        
        $checkoutStarted = isset($_SESSION['sso_checkout_started']) && (bool)$_SESSION['sso_checkout_started'] === true;
        if (isset($_SESSION['customer_id'])
            && ($_SESSION['customer_status']['customer_status_id'] ?? null) !== DEFAULT_CUSTOMERS_STATUS_ID_GUEST) {
            // customer logged in, redirect to home page
            $redirectUrl = HTTP_SERVER . DIR_WS_CATALOG;
            if ($checkoutStarted === true) {
                $redirectUrl .= 'checkout_shipping.php?amazonpay=start';
            } elseif (isset($_SESSION['sso_redirect_after_login'])) {
                $redirectUrl = $_SESSION['sso_redirect_after_login'];
                unset($_SESSION['sso_redirect_after_login']);
            }
        } else {
            // new customer, redirect to customer registration
            $redirectUrl = HTTP_SERVER . DIR_WS_CATALOG . 'shop.php?do=CreateRegistree';
            if ($checkoutStarted) {
                $redirectUrl .= '&checkout_started=1';
            }
        }
        
        return MainFactory::create('RedirectHttpControllerResponse', $redirectUrl);
    }
    
    
    /**
     * Deletes a connection between a customer and an SSO identity.
     *
     * @return array|bool|RedirectHttpControllerResponse
     * @throws Exception
     */
    public function actionDeleteSsoConnection()
    {
        $service = $this->_getPostData('service');
        if (empty($_SESSION['customer_id'])
            || $_SESSION['customers_status']['customers_status_id'] === DEFAULT_CUSTOMERS_STATUS_ID_GUEST) {
            throw new Exception('You are not logged in. Just what do you think you’re doing, Dave?');
        }
        $customers_id = (int)$_SESSION['customer_id'];
        if (array_key_exists($service, $this->validServices)) {
            $issuer = $this->validServices[$service];
            $db     = StaticGXCoreLoader::getDatabaseQueryBuilder();
            $db->delete('customers_sso', ['customers_id' => $customers_id, 'issuer' => $issuer]);
        }
        
        $redirectUrl = xtc_href_link('account.php');
        
        return MainFactory::create('RedirectHttpControllerResponse', $redirectUrl);
    }
    
    
    /**
     * Creates instance of SSO service implementation.
     *
     * @param string $service (google|facebook|paypal|amazon)
     *
     * @return AmazonSingleSignonService|array|bool|FacebookSingleSignonService|GoogleSingleSignonService|PaypalSingleSignonService
     * @throws Exception
     */
    protected function createSingleSignonService($service)
    {
        $ssoConfiguration = MainFactory::create('SingleSignonConfigurationStorage');
        if ($service === 'google' && (bool)$ssoConfiguration->get('services/google/active') === true) {
            $ssoService = MainFactory::create('GoogleSingleSignonService',
                                              $ssoConfiguration->get('services/google/clientId'),
                                              $ssoConfiguration->get('services/google/clientSecret'),
                                              $ssoConfiguration->get('services/google/redirectUri'));
        } elseif ($service === 'facebook' && (bool)$ssoConfiguration->get('services/facebook/active') === true) {
            $ssoService = MainFactory::create('FacebookSingleSignonService',
                                              $ssoConfiguration->get('services/facebook/clientId'),
                                              $ssoConfiguration->get('services/facebook/clientSecret'),
                                              $ssoConfiguration->get('services/facebook/redirectUri'));
        } elseif ($service === 'paypal' && (bool)$ssoConfiguration->get('services/paypal/active') === true) {
            $paypalMode = $ssoConfiguration->get('services/paypal/mode');
            if ($paypalMode === 'live') {
                $ssoService = MainFactory::create('PaypalSingleSignonService',
                                                  $ssoConfiguration->get('services/paypal/clientId'),
                                                  $ssoConfiguration->get('services/paypal/clientSecret'),
                                                  $ssoConfiguration->get('services/paypal/redirectUri'));
            } else {
                $ssoService = MainFactory::create('PaypalSingleSignonService',
                                                  $ssoConfiguration->get('services/paypal/clientIdSandbox'),
                                                  $ssoConfiguration->get('services/paypal/clientSecretSandbox'),
                                                  $ssoConfiguration->get('services/paypal/redirectUri'));
            }
            $ssoService->setMode($paypalMode);
        } elseif ($service === 'amazon' && (bool)$ssoConfiguration->get('services/amazon/active') === true) {
            $amazonMode = $ssoConfiguration->get('services/amazon/mode');
            $ssoService = MainFactory::create('AmazonSingleSignonService',
                                              $ssoConfiguration->get('services/amazon/clientId'),
                                              $ssoConfiguration->get('services/amazon/clientSecret'),
                                              $ssoConfiguration->get('services/amazon/redirectUri'));
            $ssoService->setMode($amazonMode);
        } else {
            throw new Exception('Invalid or unsupported service');
        }
        
        return $ssoService;
    }
    
    
    /**
     * Looks up customers_id by SSO issuer and subject values; returns false if SSO connection not found.
     *
     * @param $issuer
     * @param $subject
     *
     * @return bool|int
     */
    protected function findCustomerByIssuerAndSubject($issuer, $subject)
    {
        $db = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $db->query('DELETE from `customers_sso` WHERE customers_id NOT IN (SELECT customers_id from customers)');
        $ssoCustomersQuery = $db->get_where('customers_sso', ['issuer' => $issuer, 'subject' => $subject]);
        $customerId        = false;
        foreach ($ssoCustomersQuery->result_array() as $ssoCustomer) {
            $customerId = $ssoCustomer['customers_id'];
        }
        
        return $customerId;
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
        
        if (count($customers) === 1) {
            $customerId = array_pop($customers)->getId();
        }
        
        return $customerId;
    }
    
    
    /**
     * Renders template.
     *
     * @param       $templateFile
     * @param array $content
     *
     * @return string
     * @throws Exception
     */
    protected function renderTemplate($templateFile, $content = [])
    {
        $realTemplateFile = realpath(__DIR__ . '/' . $templateFile);
        if ($realTemplateFile === false) {
            throw new Exception('invalid template file ' . $templateFile);
        }
        ob_start();
        require $realTemplateFile;
        $output = ob_get_clean();
        
        return $output;
    }
    
    
    /**
     * Stores SSO data to create customer’s SSO connection
     *
     * @param $customersId
     * @param $issuer
     * @param $subject
     */
    protected function storeSSOData($customersId, $issuer, $subject)
    {
        $db = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $db->replace('customers_sso',
                     [
                         'customers_id' => $customersId,
                         'issuer'       => $issuer,
                         'subject'      => $subject,
                     ]);
    }
}
