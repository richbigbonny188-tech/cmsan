<?php
/* --------------------------------------------------------------
   GambioHubStartSession.inc.php 2020-05-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class GambioHubStartSession
 */
class GambioHubStartSession extends GambioHubStartSession_parent
{
	public function proceed()
	{
        if((strtolower((string)@constant('MODULE_PAYMENT_GAMBIO_HUB_STATUS')) === 'true')
		   && (!array_key_exists('gambio_hub_session_key', $_SESSION) || !$this->check_cart_id()))
		{
			$checkoutHelper = MainFactory::create('HubCheckoutHelper');
			$shopUrl        = HTTP_SERVER . DIR_WS_CATALOG;
			$languageCode   = new LanguageCode(new StringType(strtoupper(DEFAULT_LANGUAGE)));
			
			$checkoutHelper->startSession($shopUrl, $languageCode);
		}
		
		return parent::proceed();
	}
}
