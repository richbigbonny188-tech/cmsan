<?php

/* --------------------------------------------------------------
  ApiV2AuthenticatorOverload.inc.php 2018-05-24
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2018 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Class ApiV2AuthenticatorOverload
 *
 * This overload added the functionality to log admin activity into log files.
 * The data contains information about name, surname, id ,Call type, email, requests url , request parameter and time
 * from current admin.
 */
class ApiV2AuthenticatorOverload extends ApiV2AuthenticatorOverload_parent
{
	public function authorize($controllerName)
	{
		parent::authorize($controllerName);
		
		if($this->authenticatedApiCall())
		{
			$this->runAdminApiLogProcess();
		}
	}
	
	
	/**
	 * Checks condition for authenticated api call.
	 *
	 * @return bool
	 */
	private function authenticatedApiCall()
	{
		return $_SERVER !== null && isset($_SERVER['PHP_AUTH_USER']);
	}
	
	
	/**
	 * Returns array of current admin data.
	 *
	 * @return array
	 */
	private function getAdminUser()
	{
		$adminEmail = $_SERVER['PHP_AUTH_USER'];
		$db         = StaticGXCoreLoader::getDatabaseQueryBuilder();
		
		return $db->select('customers_id, customers_firstname, customers_lastname, customers_email_address')
		          ->from(TABLE_CUSTOMERS)
		          ->where('customers_email_address', $adminEmail)
		          ->get()
		          ->result_array()[0];
	}
	
	
	/**
	 * Runs admin api call log process.
	 */
	private function runAdminApiLogProcess()
	{
		$adminData = $this->getAdminUser();
		if($this->getAdminLogStatus($adminData))
		{
			$adminLog = $this->createAdminLog($adminData);
			$this->logAdminActivity($adminLog);
		}
	}
	
	
	/**
	 * Returns the admin activity log status of current admin.
	 *
	 * @param array $adminData Data array of admin information.
	 *
	 * @return bool Current admin log status.
	 */
	private function getAdminLogStatus(array $adminData)
	{
		return (bool)StaticGXCoreLoader::getService('UserConfiguration')
		                               ->getUserConfiguration(new IdType($adminData['customers_id']),
		                                                      'admin_activity_status');
	}
	
	
	/**
	 * Creates an admin activity log from current session an server data.
	 *
	 * @param array $adminData Data array with admin information.
	 *
	 * @return string Admin activity log.
	 */
	private function createAdminLog(array $adminData)
	{
		return <<< EOT

Date and time : {$this->createRequestTime()->format('d.m.Y  H:i:s')}
Call          : API

Admin name    : {$adminData['customers_firstname']} {$adminData['customers_lastname']}
Admin email   : {$adminData['customers_email_address']}
Admin ID      : {$adminData['customers_id']}
Admin IP      : {$_SERVER['REMOTE_ADDR']}

Requests      : {$_SERVER['REQUEST_METHOD']}
Parameters    : {$_SERVER['QUERY_STRING']}

URL           : {$this->getUrl()}

----------------------------------------------------------------------------------------------------------------
EOT;
	}
	
	
	/**
	 * Logs an admin log as admin activity log file.
	 *
	 * @param $adminLog string Admin log string.
	 */
	private function logAdminActivity($adminLog)
	{
		$logControl = MainFactory::create_object('LogControl', [], true);
		$logControl->write_text_log($adminLog, 'admin_activity');
	}
	
	
	/**
	 * Creates a datetime object with current time and default stetted timezone.
	 *
	 * @return \DateTime Current Date time object in default timezone.
	 */
	private function createRequestTime()
	{
		return new DateTime('now', new DateTimeZone(date_default_timezone_get()));
	}
	
	
	/**
	 * Returns an url string from server global.
	 *
	 * @return string Url string.
	 */
	private function getUrl()
	{
		return (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}
	
}