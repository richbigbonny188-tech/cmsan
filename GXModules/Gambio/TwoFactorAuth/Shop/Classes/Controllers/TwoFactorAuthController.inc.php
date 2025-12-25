<?php

/* --------------------------------------------------------------
   TwoFactorAuthController.inc.php 2018-12-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class representing a two-factor-authorization front-end controller
 */
class TwoFactorAuthController extends HttpViewController
{
    /**
     * Two-factor-authentication service factory
     * @var TwoFactorAuthServiceFactory
     */
    protected $twoFactorAuthServiceFactory;
    
    /**
     * Reference to the message stack
     * @var messageStack_ORIGIN
     */
    protected $messageStack;
    
    /**
     * Language text manager
     * @var LanguageTextManager
     */
    protected $languageTextManager;
    
    /**
     * Customer service factory
     * @var CustomerServiceFactory
     */
    protected $customerServiceFactory;
    
    /**
     * Customer ID
     * @var IdType
     */
    protected $customerId;
    
    
    /**
     * Initialize
     * @throws BadMethodCallException Module not installed or not logged in
     */
    public function init()
    {
        $isModuleInstalled = (bool)gm_get_conf('MODULE_CENTER_TWOFACTORAUTH_INSTALLED');
        $isLoggedIn        = isset($_SESSION['customer_id']);
        
        if (!$isModuleInstalled) {
            throw new BadMethodCallException('Module not installed');
        }
        
        if (!$isLoggedIn) {
            xtc_redirect(xtc_href_link(FILENAME_LOGIN, '', 'SSL'));
        }
        
        $this->twoFactorAuthServiceFactory = MainFactory::create('TwoFactorAuthServiceFactory');
        $this->languageTextManager         = MainFactory::create('LanguageTextManager', 'twofactorauth');
        $this->messageStack                = $GLOBALS['messageStack'];
        $this->customerServiceFactory      = MainFactory::create('CustomerServiceFactory',
                                                                 StaticGXCoreLoader::getDatabaseQueryBuilder());
        $this->customerId                  = new IdType((int)$_SESSION['customer_id']);
    }
    
    
    /**
     * Redirect either to the edit view or to the configuration view
     * @return RedirectHttpControllerResponse Edit or configuration view redirection
     */
    public function actionDefault()
    {
        $url = $this->isUsing2fa() ? DIR_WS_CATALOG . TwoFactorAuthControllerConfiguration::EDIT_URL : DIR_WS_CATALOG
                                                                                                       . TwoFactorAuthControllerConfiguration::CONFIGURATION_URL;
        
        return MainFactory::create('RedirectHttpControllerResponse', $url);
    }
    
    
    /**
     * Return the edit view if customer is using 2FA
     * Otherwise a redirection to the configuration view is returned
     * @return RedirectHttpControllerResponse|HttpControllerResponse Redirection or edit view
     */
    public function actionEdit()
    {
        if (!$this->isUsing2fa()) {
            return MainFactory::create('RedirectHttpControllerResponse',
                                       DIR_WS_CATALOG . TwoFactorAuthControllerConfiguration::CONFIGURATION_URL);
        }
        
        $template = new NonEmptyStringType('edit.html');
        
        $data = MainFactory::create('KeyValueCollection',
                                    [
                                        'url' => DIR_WS_CATALOG . TwoFactorAuthControllerConfiguration::DEACTIVATION_URL
                                    ]);
        
        return $this->responseByTemplateAndData($template, $data);
    }
    
    
    /**
     * Return the configuration view if customer is not using 2FA
     * Otherwise a redirection to the edit view is returned
     * @return RedirectHttpControllerResponse|HttpControllerResponse Redirection or configuration view
     */
    public function actionConfigure()
    {
        if ($this->isUsing2fa()) {
            return MainFactory::create('RedirectHttpControllerResponse',
                                       DIR_WS_CATALOG . TwoFactorAuthControllerConfiguration::EDIT_URL);
        }
        
        $template = new NonEmptyStringType('configuration.html');
        $step     = $this->_getQueryParameter('step');
        
        if (!$step) {
            $url = DIR_WS_CATALOG . TwoFactorAuthControllerConfiguration::CONFIGURATION_URL . '&step=1';
            
            return MainFactory::create('RedirectHttpControllerResponse', $url);
        }
        
        $typedStep = new ConfigurationStep($step);
        $data      = $this->templateDataForConfigurationStep($typedStep);
        
        return $this->responseForConfigurationStep($typedStep, $template, $data);
    }
    
    
    /**
     * Deactivate the two-factor-authentication for the current customer
     */
    public function actionDeactivate()
    {
        if ($this->isUsing2fa()) {
            $this->twoFactorAuthServiceFactory->delete()->secretAndSafetyFileForCustomer($this->customerId);
            $this->messageStack->add_session(TwoFactorAuthControllerConfiguration::MESSAGE_STACK_CLASS_NAME,
                                             $this->languageTextManager->get_text('deactivated'),
                                             'success');
        }
        
        return MainFactory::create('RedirectHttpControllerResponse',
                                   DIR_WS_CATALOG . TwoFactorAuthControllerConfiguration::ACCOUNT_SETTINGS_URL);
    }
    
    
    /**
     * Return template data for the provided step
     *
     * @param ConfigurationStep $step Current step
     *
     * @return KeyValueCollection Template data
     */
    protected function templateDataForConfigurationStep(ConfigurationStep $step)
    {
        $stepAsInteger = $step->asInt();
        
        $data = [
            'step' => $stepAsInteger
        ];
        
        switch ($stepAsInteger) {
            case 1:
                $data['next'] = DIR_WS_CATALOG . TwoFactorAuthControllerConfiguration::CONFIGURATION_URL . '&step=2';
                break;
            case 2:
                $data['next']   = DIR_WS_CATALOG . TwoFactorAuthControllerConfiguration::CONFIGURATION_URL . '&step=3';
                $data['secret'] = $this->twoFactorAuthServiceFactory->read()->randomSecret()->code();
                $data['issuer'] = defined('STORE_NAME') ? STORE_NAME : TwoFactorAuthControllerConfiguration::DEFAULT_ISSUER;
                $data['user']   = (string)$this->customerServiceFactory->getCustomerService()
                    ->getCustomerById($this->customerId)
                    ->getEmail();
                
                break;
            case 3:
                $data['previous'] = DIR_WS_CATALOG . TwoFactorAuthControllerConfiguration::CONFIGURATION_URL
                                    . '&step=2';
                $data['next']     = DIR_WS_CATALOG . TwoFactorAuthControllerConfiguration::CONFIGURATION_URL
                                    . '&step=4';
                $data['method']   = 'POST';
                break;
        }
        
        return MainFactory::create('KeyValueCollection', $data);
    }
    
    
    /**
     * Return response for the provided step after processing the data
     *
     * @param ConfigurationStep       $step     Current step
     * @param NonEmptyStringType      $template Template
     * @param KeyValueCollection|null $data     Data
     *
     * @return HttpControllerResponse|RedirectHttpControllerResponse Response
     */
    protected function responseForConfigurationStep(
        ConfigurationStep $step,
        NonEmptyStringType $template,
        KeyValueCollection $data = null
    ) {
        switch ($step->asInt()) {
            case 2:
                $_SESSION[TwoFactorAuthControllerConfiguration::SECRET_SESSION_KEY] = $data->getValue('secret');
                break;
            case 4:
                $token = $this->_getPostData('token');
                $token = new NonEmptyStringType($token);
                $token = AuthToken::withCode($token);
                
                $secret = $_SESSION[TwoFactorAuthControllerConfiguration::SECRET_SESSION_KEY];
                $secret = new NonEmptyStringType($secret);
                $secret = AuthSecret::withCode($secret);
                
                $isProvidedTokenValid = $this->twoFactorAuthServiceFactory->read()->tokenValidityForSecret($token,
                                                                                                           $secret);
                
                unset($_SESSION[TwoFactorAuthControllerConfiguration::SECRET_SESSION_KEY]);
                
                if (!$isProvidedTokenValid) {
                    $this->messageStack->add_session(TwoFactorAuthControllerConfiguration::MESSAGE_STACK_CLASS_NAME,
                                                     $this->languageTextManager->get_text('token_invalid'));
                    
                    return MainFactory::create('RedirectHttpControllerResponse',
                                               DIR_WS_CATALOG . TwoFactorAuthControllerConfiguration::CONFIGURATION_URL
                                               . '&step=2');
                }
                
                $this->twoFactorAuthServiceFactory->write()
                    ->secretForCustomer($this->customerId, $secret)
                    ->safetyFileForCustomer($this->customerId);
                
                $this->messageStack->add_session(TwoFactorAuthControllerConfiguration::MESSAGE_STACK_CLASS_NAME,
                                                 $this->languageTextManager->get_text('activated'),
                                                 'success');
                
                return MainFactory::create('RedirectHttpControllerResponse',
                                           DIR_WS_CATALOG . TwoFactorAuthControllerConfiguration::ACCOUNT_SETTINGS_URL);
                
                break;
        }
        
        return $this->responseByTemplateAndData($template, $data);
    }
    
    
    /**
     * Return whether the customer is using 2FA
     * @return bool Customer 2FA usage status
     */
    protected function isUsing2fa()
    {
        return $this->twoFactorAuthServiceFactory->read()->usageStatusForCustomer($this->customerId);
    }
    
    
    /**
     * Return a response by the provided template and view data
     *
     * @param NonEmptyStringType      $template Template file name residing in
     *                                          'GXModules/Gambio/TwoFactorAuth/Shop/Html'
     * @param KeyValueCollection|null $data     View data
     *
     * @return HttpControllerResponse|RedirectHttpControllerResponse Response
     */
    protected function responseByTemplateAndData(NonEmptyStringType $template, KeyValueCollection $data = null)
    {
        $contentView = MainFactory::create('TwoFactorAuthThemeContentView');
        $contentView->set_content_template($template->asString());
        
        if ($data !== null) {
            foreach ($data->getIterator() as $key => $value) {
                $contentView->set_content_data($key, $value);
            }
        }
        
        if ($this->messageStack->size(TwoFactorAuthControllerConfiguration::MESSAGE_STACK_CLASS_NAME)) {
            $contentView->set_content_data('message',
                                           $this->messageStack->output(TwoFactorAuthControllerConfiguration::MESSAGE_STACK_CLASS_NAME));
        }
        
        $GLOBALS['breadcrumb']->add($this->languageTextManager->get_text('HEADER_TITLE_TOP', 'general'));
        $GLOBALS['breadcrumb']->add($this->languageTextManager->get_text('NAVBAR_TITLE_1_ACCOUNT_EDIT', 'general'),
                                    'account.php');
        $GLOBALS['breadcrumb']->add($this->languageTextManager->get_text('title'),
                                    DIR_WS_CATALOG . TwoFactorAuthControllerConfiguration::EDIT_URL);
        
        $contentControl = MainFactory::create_object('LayoutContentControl');
        $contentControl->set_data('GET', $this->_getQueryParametersCollection()->getArray());
        $contentControl->set_data('POST', $this->_getPostDataCollection()->getArray());
        $contentControl->set_('coo_breadcrumb', $GLOBALS['breadcrumb']);
        $contentControl->set_('coo_product', $GLOBALS['product']);
        $contentControl->set_('coo_xtc_price', $GLOBALS['xtPrice']);
        $contentControl->set_('c_path', $GLOBALS['cPath']);
        $contentControl->set_('main_content', $contentView->get_html());
        $contentControl->set_('request_type', $GLOBALS['request_type']);
        $contentControl->proceed();
        
        return MainFactory::create('HttpControllerResponse', $contentControl->get_response());
    }
}