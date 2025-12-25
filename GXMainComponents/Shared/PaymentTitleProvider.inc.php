<?php

/* --------------------------------------------------------------
   PaymentTitleProvider.inc.php 2017-04-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class PaymentTitleProvider
 *
 * @category System
 * @package  Shared
 */
class PaymentTitleProvider extends ModuleTitleProvider
{
    /**
     * @var string
     */
    protected static $type = 'payment';
    
    
    /**
     * Returns the title of a payment.
     * This method is a workaround, if no payment exists and a voucher was used.
     *
     * @param string $methodName        Name of payment method.
     * @param string $languageDirectory (Optional) Language files directory. If not set, the sessions language is used.
     *
     * @return string
     */
    public static function getTitle($methodName, $languageDirectory = '')
    {
        if ($methodName !== 'voucher') {
            return parent::getTitle($methodName, $languageDirectory);
        }
        
        static::_validateLanguageDirectoryArgument($languageDirectory);
        $languageProvider = MainFactory::create('LanguageProvider', StaticGXCoreLoader::getDatabaseQueryBuilder());
        
        return static::_getLanguageTextManager()->get_text('VOUCHER',
                                                           'admin_orders',
                                                           $languageProvider->getIdByDirectory(new StringType($languageDirectory)));
    }
}