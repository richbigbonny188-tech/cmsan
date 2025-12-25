<?php
/* --------------------------------------------------------------
   SSORemoveCookiesApplicationTopPrimalExtenderComponent.inc.php 2019-06-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class SSORemoveCookiesApplicationTopPrimalExtenderComponent
    extends SSORemoveCookiesApplicationTopPrimalExtenderComponent_parent
{
    private $cookies = ['amazon_Login_state_cache', 'amazon_Login_accessToken'];
    
    
    public function proceed()
    {
        if (empty($_COOKIE['amazon_pay_checkout'])
            && (!isset($_SESSION['ssoData']['iss']) || $_SESSION['ssoData']['iss'] !== 'amazon.com')
            && $this->cookiesAreSet()) {
            $this->clearAmazonCookies();
        }
        
        return parent::proceed();
    }
    
    
    private function cookiesAreSet()
    {
        $cookieSet = false;
        foreach ($this->cookies as $cookieName) {
            $cookieSet = $cookieSet || isset($_COOKIE[$cookieName]);
        }
        
        return $cookieSet;
    }
    
    
    private function clearAmazonCookies()
    {
        foreach ($this->cookies as $cookieName) {
            setcookie($cookieName, '', time() - 3600);
            setcookie($cookieName, '', time() - 3600, '/', '', true);
        }
    }
}
