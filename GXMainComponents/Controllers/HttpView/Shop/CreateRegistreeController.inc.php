<?php
/* --------------------------------------------------------------
   CreateRegistreeController.inc.php 2024-04-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2024 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpViewController');

/**
 * Class CreateRegistreeController
 *
 * @category System
 * @package  HttpViewControllers
 */
class CreateRegistreeController extends HttpViewController
{
    protected bool $forceNextRedirect = false;
    
    /**
     * @return HttpControllerResponse|RedirectHttpControllerResponse
     */
    public function actionDefault()
    {
        if (isset($_SESSION['customer_id'])) {
            return MainFactory::create('RedirectHttpControllerResponse', xtc_href_link(FILENAME_ACCOUNT, '', 'SSL'));
        }
        
        return $this->_getResponse($this->_getMainContentHtml());
    }
    
    
    /**
     * TODO use of MainFactory is only an interim solution
     *
     * @return HttpControllerResponse|RedirectHttpControllerResponse
     */
    public function actionProceed()
    {
        if (isset($_SESSION['customer_id'])) {
            return MainFactory::create('RedirectHttpControllerResponse', xtc_href_link(FILENAME_ACCOUNT, '', 'SSL'));
        }
        
        // proceed with standard page, if actionProcees is not called by a POST request
        if (!count($this->_getPostDataCollection()->getArray())) {
            return $this->actionDefault();
        }
        
        /**
         * @var CountryService $countryService
         */
        $countryService     = StaticGXCoreLoader::getService('Country');
        $customerCollection = null;
        
        try {
            $inputTransformer     = MainFactory::create('CustomerInputToCollectionTransformer');
            $customerCollection   = $inputTransformer->getRegistreeCollectionFromInputArray($this->_getPostDataCollection()
                                                                                                ->getArray(),
                                                                                            $countryService);
            $createAccountProcess = MainFactory::create('CreateAccountProcess',
                                                        StaticGXCoreLoader::getService('CustomerWrite'),
                                                        $countryService);
            
            $createAccountProcess->proceedRegistree($customerCollection,
                                                    MainFactory::create('GMLogoManager', 'gm_logo_mail'));
            
            $redirectUrl = FILENAME_SHOPPING_CART;
            
            if ($this->_getQueryParameter('checkout_started') === '1') {
                $redirectUrl = FILENAME_CHECKOUT_SHIPPING;
            }
            
            return MainFactory::create('RedirectHttpControllerResponse', xtc_href_link($redirectUrl, '', 'SSL'));
        } catch (InvalidCustomerDataException $e) {
            return $this->_getResponse($this->_getMainContentHtml($customerCollection,
                                                                  $e->getErrorMessageCollection(),
                                                                  true));
        } catch (InvalidArgumentException $e) {
            // Catches forged requests with invalid content to prevent that the error log will pile up if a bot goes rouge
            
            // TODO replace new KeyValueCollection(array()) with something meaningful
            return $this->_getResponse($this->_getMainContentHtml($customerCollection,
                                                                  new KeyValueCollection([]),
                                                                  true));
        } catch (SuperhumanRegistrationSpeedException $e) {
            // it is likely that a bot did this, so pretend that registration worked, but don't actually register
            // anything. then send user to shopping cart
            $res = MainFactory::create('RedirectHttpControllerResponse', xtc_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
            $this->forceNextRedirect = true;
            return $res;
        }
    }
    
    
    /**
     * @param string $p_mainContentHtml
     *
     * @return HttpControllerResponse|RedirectHttpControllerResponse
     */
    protected function _getResponse($p_mainContentHtml)
    {
        $GLOBALS['breadcrumb']->add(NAVBAR_TITLE_CREATE_ACCOUNT,
                                    xtc_href_link('shop.php',
                                                  xtc_get_all_get_params(['do']) . '&do=CreateRegistree',
                                                  'SSL'));
        
        $layoutContentControl = MainFactory::create_object('LayoutContentControl');
        $layoutContentControl->set_data('GET', $this->_getQueryParametersCollection()->getArray());
        $layoutContentControl->set_data('POST', $this->_getPostDataCollection()->getArray());
        $layoutContentControl->set_('coo_breadcrumb', $GLOBALS['breadcrumb']);
        $layoutContentControl->set_('coo_product', $GLOBALS['product']);
        $layoutContentControl->set_('coo_xtc_price', $GLOBALS['xtPrice']);
        $layoutContentControl->set_('c_path', $GLOBALS['cPath']);
        $layoutContentControl->set_('main_content', $p_mainContentHtml);
        $layoutContentControl->set_('request_type', $GLOBALS['request_type']);
        $layoutContentControl->proceed();
        
        $redirectUrl = $layoutContentControl->get_redirect_url();
        if (!empty($redirectUrl)) {
            return MainFactory::create('RedirectHttpControllerResponse', $redirectUrl);
        }
        
        return MainFactory::create('HttpControllerResponse', $layoutContentControl->get_response());
    }
    
    
    /**
     * @param KeyValueCollection $customerCollection
     * @param KeyValueCollection $errorMessageCollection
     * @param bool               $p_process
     *
     * @return string
     */
    protected function _getMainContentHtml(
        KeyValueCollection $customerCollection = null,
        KeyValueCollection $errorMessageCollection = null,
        $p_process = false
    ) {
        $customerArray = [];
        if ($customerCollection !== null) {
            $customerArray = $customerCollection->getArray();
        }
        
        $errorMessages = [];
        if ($errorMessageCollection !== null) {
            $errorMessages = $errorMessageCollection->getArray();
        }
        
        $createAccountContentView = MainFactory::create_object('CreateAccountThemeContentView');
        
        $createAccountContentView->set_('guest_account', false);
        $createAccountContentView->set_('customer_data_array', $customerArray);
        $createAccountContentView->set_('error_array', $errorMessages);
        $createAccountContentView->set_('process', $p_process);
        $createAccountContentView->set_('checkout_started', (int)$this->_getQueryParameter('checkout_started'));
        
        return $createAccountContentView->get_html();
    }
}