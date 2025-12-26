<?php
/*--------------------------------------------------------------------------------------------------
    CookieManagerInterface.php 2019-12-19
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

/**
 * Interface CookieManagerInterface
 */
interface CookieConsentManagerInterface
{
    /**
     * @return CookieConfigurationList
     */
    public function cookiesList(): CookieConfigurationList;
    
    
    /**
     * @return CookieConfigurationList
     */
    public function vendorCookiesList(): CookieConfigurationList;
    
    
    /**
     * @param int $vendorId
     */
    public function deactivateVendor(int $vendorId) : void;
    
    
    /**
     * @param int $purpose
     */
    public function deactivatePurpose(int $purpose) : void;
    
    
    /**
     * @param int $feature
     */
    public function deactivateFeature(int $feature) : void;
}