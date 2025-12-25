<?php
/* --------------------------------------------------------------
   BrowserLoggerLoaderHeaderExtender.inc.php 2023-07-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class BrowserLoggerLoaderHeaderThemeExtender extends BrowserLoggerLoaderHeaderThemeExtender_parent
{
    public function get_html()
    {
        $htmlContent= parent::get_html();
        
        if (($_SESSION['payment'] ?? null) !== 'gambio_hub'
            || (defined('MODULE_PAYMENT_GAMBIO_HUB_STATUS')
                && strtolower(MODULE_PAYMENT_GAMBIO_HUB_STATUS) !== 'true')) {
            return $htmlContent;
        }
    
        return $htmlContent . '<script src="GXModules/Gambio/Hub/Shop/Javascript/BrowserLoggerLoader.js" async></script>';
    }
}
