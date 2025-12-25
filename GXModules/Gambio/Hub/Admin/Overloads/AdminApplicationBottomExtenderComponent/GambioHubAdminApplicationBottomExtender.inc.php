<?php
/* --------------------------------------------------------------
   GambioHubAdminApplicationBottomExtender.inc.php 2019-01-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class GambioHubAdminApplicationBottomExtender
 */
class GambioHubAdminApplicationBottomExtender extends GambioHubAdminApplicationBottomExtender_parent
{
	/**
	 * Performs Gambio Hub related tasks right before the response is ready.
	 */
	public function proceed()
	{
		parent::proceed();
		
		$this->_addPaymentNavigationTab();
	}
	
	
	/**
	 * Adds the payment navigation tab if the correct permissions are set.
	 *
	 * @return $this Returns same instance for chained method calls.
	 */
	protected function _addPaymentNavigationTab()
	{
		$installedVersion = gm_get_conf('INSTALLED_VERSION');
		$hubAssetHelper   = MainFactory::create('HubAssetHelper', $installedVersion);
		$requestUri       = basename($_SERVER['REQUEST_URI']);
		$scriptName       = substr($requestUri, 0,
			(strpos($requestUri, '?') !== false ? strpos($requestUri, '?') : strlen($requestUri)));
		
		if($scriptName === 'modules.php' && $_GET['set'] === 'payment')
		{
			if($this->_verifyAdminAccessPermissions())
			{
				$postfix = file_exists(DIR_FS_CATALOG . '.dev-environment') ? '' : '.min';
				$script  = '<script type="text/javascript" src="' . DIR_WS_CATALOG
				           . $hubAssetHelper->getScriptsBaseUrl() . '/extenders/payment_navigation_tab' . $postfix
				           . '.js"></script>';
				
				echo $script;
			}
		}
		
		return $this;
	}
	
	
	/**
	 * Verifies admin access permissions for displaying the payment navigation tab.
	 *
	 * @return bool
	 */
	protected function _verifyAdminAccessPermissions()
	{
		if(!class_exists(AdminAccessService::class))
		{
			return true;
		}
		
		/** @var \AdminAccessService $adminAccessService */
		$adminAccessService = StaticGXCoreLoader::getService('AdminAccess');
		
		return $adminAccessService->checkReadingPermissionForController(new NonEmptyStringType('HubConfiguration'),
		                                                                new IdType((int)$_SESSION['customer_id']));
	}
}
