<?php
/*--------------------------------------------------------------------------------------------------
    CookieConsentPanelApplicationTop.inc.php 2020-01-09
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

/**
 * Class CookieConsentPanelApplicationTop
 */
class CookieConsentPanelApplicationTop extends CookieConsentPanelApplicationTop_parent
{
    /**
     * @return CookieConsentPanelFactoryInterface
     */
    protected function  createCookiePanelFactory() : CookieConsentPanelFactoryInterface {
        /**
         * Method Factory is being used here because the is no way to inject the factory from the extension
         */
        return MainFactory::create(CookieConsentPanelFactory::class);
    }
    
    function proceed()
    {
        $factory             = $this->createCookiePanelFactory();
        $factory->createManager();
        
        parent::proceed();
    }
    
}