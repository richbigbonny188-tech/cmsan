<?php
/*--------------------------------------------------------------------------------------------------
    CookiesConsentStore.php 2023-05-03
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2023 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

/**
 * Class CookiesConsentSsoStore
 */
class CookiesConsentSsoStore
{
    /**
     * @var CookieConfigurationInterface
     */
    protected static $amazon = null;
    /**
     * @var CookieConfigurationInterface
     */
    protected static $facebook = null;
    /**
     * @var CookieConfigurationInterface
     */
    protected static $google = null;
    /**
     * @var CookieConfigurationInterface
     */
    protected static $payPal = null;
    
    /**
     * @return CookieConfigurationInterface|null
     */
    public static function amazon(): ?CookieConfigurationInterface
    {
        return self::$amazon;
    }
    
    
    /**
     * @return CookieConfigurationInterface|null
     */
    public static function facebook(): ?CookieConfigurationInterface
    {
        return self::$facebook;
    }
    
    
    /**
     * @return CookieConfigurationInterface|null
     */
    public static function payPal(): ?CookieConfigurationInterface
    {
        return self::$payPal;
    }
    
    
    /**
     * @return CookieConfigurationInterface|null
     */
    public static function google(): ?CookieConfigurationInterface
    {
        return self::$google;
    }
    
    
    /**
     * @param CookieConfigurationInterface $amazon
     */
    public static function setAmazon(CookieConfigurationInterface $amazon): void
    {
        self::$amazon = $amazon;
    }
    
    
    /**
     * @param CookieConfigurationInterface $facebook
     */
    public static function setFacebook(CookieConfigurationInterface $facebook): void
    {
        self::$facebook = $facebook;
    }
    
    
    /**
     * @param CookieConfigurationInterface $google
     */
    public static function setGoogle(CookieConfigurationInterface $google): void
    {
        self::$google = $google;
    }
    
    
    /**
     * @param CookieConfigurationInterface $payPal
     */
    public static function setPayPal(CookieConfigurationInterface $payPal): void
    {
        self::$payPal = $payPal;
    }
    
}