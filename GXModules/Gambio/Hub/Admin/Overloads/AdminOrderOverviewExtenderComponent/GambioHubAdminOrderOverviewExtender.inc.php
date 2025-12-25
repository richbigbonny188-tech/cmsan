<?php
/* --------------------------------------------------------------
   GambioHubAdminOrderOverviewExtender.inc.php 2017-03-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class GambioHubAdminOrderOverviewExtender
 *
 * Peforms rendering operations which is required for the correct display of the Gambio Hub orders.
 */
class GambioHubAdminOrderOverviewExtender extends GambioHubAdminOrderOverviewExtender_parent
{
	/**
	 * @var \HubAssetHelper
	 */
	protected $hubAssetHelper;
	
	/**
	 * Proceed with the execution of the extender.
	 */
	public function proceed()
	{
		parent::proceed();
        
        MainFactory::create('HubNoPayOrderStatusChanger')->changeStatus();
        
        $installedVersion = gm_get_conf('INSTALLED_VERSION');
		$this->hubAssetHelper = MainFactory::create('HubAssetHelper', $installedVersion);
		
		$debug = file_exists(DIR_FS_CATALOG . '.dev-environment');
		
		if($_GET['action'] !== 'edit' || empty($_GET['oID']))
		{
			$this->_includeOrderOverviewScript($debug);
		}
	}
	
	/**
	 * Include Order Overview Script (Compatibility Mode)
	 *
	 * @param bool $debug Optional (false), whether we are in debug environment.
	 */
	protected function _includeOrderOverviewScript($debug = false)
	{
		$sql   = 'SELECT `orders_id`, `gambio_hub_module_title` FROM `orders` WHERE `gambio_hub_module_title` != ""';
		$query = xtc_db_query($sql);
		$rows  = [];
		
		while($row = xtc_db_fetch_array($query))
		{
			$rows[$row['orders_id']] = $row['gambio_hub_module_title'];
		}
		
		$postfix = $debug ? '' : '.min';
		
		$src = DIR_WS_CATALOG . $this->hubAssetHelper->getScriptsBaseUrl() . '/extenders/order_overview' . $postfix . '.js';
		
		echo '<script src="' . $src . '" data-gambio-hub-source-selector="#gambio-hub-payment-modules"></script>';
		echo '<script id="gambio-hub-payment-modules" type="application/json">' . json_encode($rows, JSON_PRETTY_PRINT
		                                                                                             | JSON_UNESCAPED_SLASHES)
		     . '</script>';
	}
}