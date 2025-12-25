<?php
/* --------------------------------------------------------------
   KlarnaHubShopContentThemeContentView.php 2018-04-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class KlarnaHubShopContentThemeContentView extends KlarnaHubShopContentThemeContentView_parent
{
	protected function add_shipping_and_payment_conditions_data()
	{
		parent::add_shipping_and_payment_conditions_data();
		
		$contentBody = $this->content_array['CONTENT_BODY'];
		
		if(empty($contentBody))
		{
			return;
		}
		
		$klarnaHubTermsAndConditions = MainFactory::create('KlarnaHubTermsAndConditions', $contentBody);
		
		$this->set_content_data('CONTENT_BODY', $klarnaHubTermsAndConditions->getContent());
	}
}
