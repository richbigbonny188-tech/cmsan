<?php
/* --------------------------------------------------------------
   GambioHubSelfpickup.inc.php 2022-05-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class GambioHubSelfpickup extends GambioHubSelfpickup_parent
{
    function quote($method = '')
    {
        $quote = parent::quote($method);
        
        if (!empty($_SESSION['PayPal2Hub'])) {
            $text   = MainFactory::create('LanguageTextManager', 'gambio_hub_paypal', $_SESSION['languages_id']);
            $quote['error'] = $text->get_text('selfpickup_paypal_error');
        }
        
        return $quote;
    }
}
