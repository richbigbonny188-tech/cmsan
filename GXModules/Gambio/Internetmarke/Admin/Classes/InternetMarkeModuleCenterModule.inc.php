<?php
/* --------------------------------------------------------------
	InternetmarkeModuleCenterModule.inc.php 2016-06-06
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class InternetMarkeModuleCenterModule extends AbstractModuleCenterModule
{
	protected $internetMarkeText;

	protected function _init()
	{
		$this->internetMarkeText = MainFactory::create('InternetMarkeText');
		$this->title             = $this->internetMarkeText->get_text('internetmarke_module_title');
		$this->description       = $this->internetMarkeText->get_text('internetmarke_module_description');
		$this->sortOrder         = 535353;
	}

	public function install()
	{
		$vouchersTable = 'CREATE TABLE IF NOT EXISTS `inetmarke_vouchers` (
			 `inetmarke_vouchers_id` int(11) NOT NULL AUTO_INCREMENT,
			 `orders_id` int(11) NOT NULL,
			 `link` text NOT NULL,
			 `walletBalance` int(11) NOT NULL,
			 `shopOrderId` varchar(16) NOT NULL,
			 `voucherId` varchar(32) NOT NULL,
			 `trackId` varchar(128) NOT NULL,
			 `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
			 PRIMARY KEY (`inetmarke_vouchers_id`),
			 KEY `orders_id` (`orders_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8';
		$this->db->query($vouchersTable);
		parent::install();
	}

}
