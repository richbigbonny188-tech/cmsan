<?php

/* --------------------------------------------------------------
   TwoFactorAuthLoginController.inc.php 2018-12-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class representing a two-factor-authorization front-end login controller
 */
class TwoFactorAuthLoginController extends HttpViewController
{
    /**
     * Language text manager
     *
     * @var LanguageTextManager
     */
    protected $languageTextManager;

    /**
     * Reference to the message stack
     *
     * @var messageStack_ORIGIN
     */
    protected $messageStack;

    /**
     * Two-factor-authentication service factory
     *
     * @var TwoFactorAuthServiceFactory
     */
    protected $twoFactorAuthServiceFactory;


    /**
     * Initialize
     *
     * @throws BadMethodCallException Module not installed
     */
    public function init()
    {
        $isModuleInstalled = (bool)gm_get_conf('MODULE_CENTER_TWOFACTORAUTH_INSTALLED');

        if (!$isModuleInstalled) {
            throw new BadMethodCallException('Module not installed');
        }

        $this->languageTextManager         = MainFactory::create('LanguageTextManager', 'twofactorauth');
        $this->messageStack                = $GLOBALS['messageStack'];
        $this->twoFactorAuthServiceFactory = MainFactory::create('TwoFactorAuthServiceFactory');
    }


    /**
     * Return the view for token prompt
     *
     * @return HttpControllerResponse Token prompt view
     * @throws Exception
     */
    public function actionDefault()
    {
        $contentView = MainFactory::create('TwoFactorAuthLoginThemeContentView');

        $getParams = array_merge($_GET, ['action' => 'process']);
        unset($getParams['do']);
        $getParams = http_build_query($getParams);

        $contentView->set_content_data('url',
                                       DIR_WS_CATALOG . TwoFactorAuthLoginControllerConfiguration::CONFIRM_URL . '&'
                                       . $getParams);

        if ($this->messageStack->size(TwoFactorAuthLoginControllerConfiguration::MESSAGE_STACK_CLASS_NAME)) {
            $contentView->set_content_data('message',
                                           $this->messageStack->output(TwoFactorAuthLoginControllerConfiguration::MESSAGE_STACK_CLASS_NAME));
        }

        $GLOBALS['breadcrumb']->add($this->languageTextManager->get_text('HEADER_TITLE_TOP', 'general'));
        $GLOBALS['breadcrumb']->add($this->languageTextManager->get_text('NAVBAR_TITLE_LOGIN', 'general'), 'login.php');

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


    /**
     * Log the customer in, when the token is valid
     *
     * @return RedirectHttpControllerResponse Redirection to the account page, when successful
     */
    public function actionConfirm()
    {
        $loginContentControl = MainFactory::create('LoginContentControl');

        $token = $this->_getPostData(TwoFactorAuthLoginControllerConfiguration::TOKEN_POST_KEY);
        $token = new NonEmptyStringType($token);
        $token = AuthToken::withCode($token);

        $customerId = (int)$_SESSION[TwoFactorAuthLoginControllerConfiguration::CUSTOMER_ID_SESSION_KEY];
        $customerId = new IdType($customerId);

        $isValidToken = $this->twoFactorAuthServiceFactory->read()->tokenValidityForCustomer($token, $customerId);

        if (!$isValidToken) {
            $this->messageStack->add_session(TwoFactorAuthLoginControllerConfiguration::MESSAGE_STACK_CLASS_NAME,
                                             $this->languageTextManager->get_text('token_invalid'));

            return MainFactory::create('RedirectHttpControllerResponse',
                                       DIR_WS_CATALOG . TwoFactorAuthLoginControllerConfiguration::TOKEN_PROMPT_URL);
        }
        $getParams = array_merge($_GET, ['action' => 'process']);
        $loginContentControl->set_data('GET', $getParams);
        $loginContentControl->set_data('POST',
                                       [
                                           TwoFactorAuthLoginControllerConfiguration::EMAIL_POST_KEY    => $_SESSION[TwoFactorAuthLoginControllerConfiguration::USER_SESSION_KEY],
                                           TwoFactorAuthLoginControllerConfiguration::PASSWORD_POST_KEY => $_SESSION[TwoFactorAuthLoginControllerConfiguration::PASSWORD_SESSION_KEY],
                                           TwoFactorAuthLoginControllerConfiguration::TOKEN_POST_KEY    => $token->code()
                                       ]);

        unset($_SESSION[TwoFactorAuthLoginControllerConfiguration::USER_SESSION_KEY], $_SESSION[TwoFactorAuthLoginControllerConfiguration::PASSWORD_SESSION_KEY], $_SESSION[TwoFactorAuthLoginControllerConfiguration::CUSTOMER_ID_SESSION_KEY]);

        $loginContentControl->proceed();

        $redirectUrl = $loginContentControl->get_redirect_url() ? : DIR_WS_CATALOG
                                                                    . TwoFactorAuthLoginControllerConfiguration::ACCOUNT_SETTINGS_URL;

        return MainFactory::create('RedirectHttpControllerResponse', $redirectUrl);
    }

}