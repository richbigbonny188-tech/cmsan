<?php
/* --------------------------------------------------------------
   SSORemoveCookiesLogoffContentControl.inc.php 2017-09-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class SSORemoveCookiesLogoffContentControl extends SSORemoveCookiesLogoffContentControl_parent
{
    public function proceed()
    {
        $this->clearAmazonCookies();
        
        return parent::proceed();
    }
    
    
    private function clearAmazonCookies()
    {
        $cookies = ['amazon_Login_state_cache', 'amazon_Login_accessToken'];
        foreach ($cookies as $cookieName) {
            if (!empty($_COOKIE[$cookieName])) {
                setcookie($cookieName, '', time() - 3600);
                setcookie($cookieName, '', time() - 3600, '/', '', true);
            }
        }
    }
}
