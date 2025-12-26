<?php
/* --------------------------------------------------------------
   CookieConsentPanelApplicationBottom.inc.php 2019-12-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class CookieConsentPanelApplicationBottom extends CookieConsentPanelApplicationBottom_parent
{
    /**
     * @type string
     */
    const KEY = 'COOKIE_CONSENT_PANEL';
    
    /**
     * @var CookieConsentPanelInstallationStatus
     */
    protected $status;
    
    
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
        if ($this->cookieConsentPanelInstallationStatus()->isInstalled()) {
            
            $factory             = $this->createCookiePanelFactory();
            $configurationScript = $factory->createView();
            
            $buffer = [];
            $buffer[] = $configurationScript->get_html() . PHP_EOL;
            $buffer[] = '<script src="GXModules/Gambio/CookieConsentPanel/Shop/Javascript/oil.js"></script>' . PHP_EOL;
            $buffer[] = '<script>!function(e){var n={};function t(o){if(n[o])return n[o].exports;var r=n[o]={i:o,l:!1,exports:{}};return e[o].call(r.exports,r,r.exports,t),r.l=!0,r.exports}t.m=e,t.c=n,t.d=function(e,n,o){t.o(e,n)||Object.defineProperty(e,n,{configurable:!1,enumerable:!0,get:o})},t.r=function(e){Object.defineProperty(e,"__esModule",{value:!0})},t.n=function(e){var n=e&&e.__esModule?function(){return e.default}:function(){return e};return t.d(n,"a",n),n},t.o=function(e,n){return Object.prototype.hasOwnProperty.call(e,n)},t.p="/",t(t.s=115)}({115:function(e,n,t){"use strict";!function(e,n){e.__cmp||(e.__cmp=function(){function t(e){if(e){var t=!0,r=n.querySelector(\'script[type="application/configuration"]#oil-configuration\');if(null!==r&&r.text)try{var a=JSON.parse(r.text);a&&a.hasOwnProperty("gdpr_applies_globally")&&(t=a.gdpr_applies_globally)}catch(e){}e({gdprAppliesGlobally:t,cmpLoaded:o()},!0)}}function o(){return!(!e.AS_OIL||!e.AS_OIL.commandCollectionExecutor)}var r=[],a=function(n,a,c){if("ping"===n)t(c);else{var i={command:n,parameter:a,callback:c};r.push(i),o()&&e.AS_OIL.commandCollectionExecutor(i)}};return a.commandCollection=r,a.receiveMessage=function(n){var a=n&&n.data&&n.data.__cmpCall;if(a)if("ping"===a.command)t(function(e,t){var o={__cmpReturn:{returnValue:e,success:t,callId:a.callId}};n.source.postMessage(o,n.origin)});else{var c={callId:a.callId,command:a.command,parameter:a.parameter,event:n};r.push(c),o()&&e.AS_OIL.commandCollectionExecutor(c)}},function(n){(e.attachEvent||e.addEventListener)("message",function(e){n.receiveMessage(e)},!1)}(a),function e(){if(!(n.getElementsByName("__cmpLocator").length>0))if(n.body){var t=n.createElement("iframe");t.style.display="none",t.name="__cmpLocator",n.body.appendChild(t)}else setTimeout(e,5)}(),a}())}(window,document)}});</script>';
            $buffer[] = '<script src="GXModules/Gambio/CookieConsentPanel/Shop/Javascript/GxCookieConsent.js"></script>' . PHP_EOL;
            
            $this->v_output_buffer[self::KEY] = implode(PHP_EOL, $buffer);
        
        }
        
        parent::proceed();
    }
    
    
    /**
     * @return CookieConsentPanelInstallationStatus
     */
    protected function cookieConsentPanelInstallationStatus(): CookieConsentPanelInstallationStatus
    {
        if ($this->status === null) {
            
            $this->status = CookieConsentPanelInstallationStatus::create();
        }
        
        return $this->status;
    }
}