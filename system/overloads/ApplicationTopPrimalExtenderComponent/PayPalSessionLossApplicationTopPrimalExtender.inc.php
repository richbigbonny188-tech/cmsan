<?php
/* --------------------------------------------------------------
   PayPalSessionLossApplicationTopPrimalExtender.inc.php 2018-06-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class PayPalSessionLossApplicationTopPrimalExtender extends PayPalSessionLossApplicationTopPrimalExtender_parent
{
    const CONTENT_GROUP_ID = 3300001;
    
    public function proceed()
    {
        parent::proceed();
        
        if(isset($GLOBALS['session_id_created']) &&
           !empty($this->v_data_array['GET']['paymentId']) &&
           strpos($GLOBALS['PHP_SELF'], '/checkout_confirmation.php') !== false)
        {
            $contentId = $this->getPayPalSessionLossContentGroupId();
            xtc_redirect(xtc_href_link('shop_content.php', 'coID=' . $contentId, 'SSL'));
        }
    }
    
    protected function getPayPalSessionLossContentGroupId()
    {
        return self::CONTENT_GROUP_ID;
    }
}
