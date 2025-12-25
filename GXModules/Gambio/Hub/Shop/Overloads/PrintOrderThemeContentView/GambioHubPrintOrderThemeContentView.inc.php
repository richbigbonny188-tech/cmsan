<?php
/* --------------------------------------------------------------
   GambioHubPrintOrderThemeContentView.inc.php 2018-10-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class GambioHubPrintOrderThemeContentView
 */
class GambioHubPrintOrderThemeContentView extends GambioHubPrintOrderThemeContentView_parent
{
	public function add_order_data()
	{
		parent::add_order_data();
		
		if($this->coo_order->info['payment_method'] === 'gambio_hub')
		{
			$query  = 'SELECT
							`gambio_hub_module`,
							`gambio_hub_module_title`
						FROM `orders`
						WHERE `orders_id` = ' . (int)$this->coo_order->info['orders_id'];
			$result = xtc_db_query($query);
			$row    = xtc_db_fetch_array($result);
			$title  = $row['gambio_hub_module_title'];
			
			if(strpos($title, 'Klarna') === false && preg_match('/^Klarna.*Hub$/', $row['gambio_hub_module']))
			{
				$title = 'Klarna ' . $title;
			}
			
			$this->content_array['PAYMENT_METHOD'] = $title;
		}
	}
}
