<?php

/* --------------------------------------------------------------
   AdminActivityLogApplicationTopExtender.inc.php 2018-05-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AdminActivityLogApplicationTopExtender
 *
 * This extender is used to log admin activity into log data files.
 * The data contains information about name, surname, id ,session id, email, requests url , request parameter and time
 * from current admin.
 */
class AdminActivityLogApplicationTopExtender extends AdminActivityLogApplicationTopExtender_parent
{
	function proceed()
	{
		parent::proceed();
		
		if($this->activityShouldBeLogged())
		{
			$this->runAdminLogProcess();
		}
	}
	
	
	/**
	 * Gets email from customers table with customers id and sets it to email session key.
	 */
	private function addEmailToSession()
	{
		$db                = StaticGXCoreLoader::getDatabaseQueryBuilder();
		$_SESSION['email'] = $db->query('select `customers_email_address`
											 from `' . TABLE_CUSTOMERS . '`
											 where `customers_id` =' . $_SESSION['customer_id'])
		                        ->result_array()[0]['customers_email_address'];
	}
	
	
	/**
	 * Creates an datetime object with current time and default stetted timezone.
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
	
	
	/**
	 * Returns the admin activity log status of current admin.
	 *
	 * @return bool Current admin log status.
	 */
	private function getAdminLogStatus()
	{
		return (bool)StaticGXCoreLoader::getService('UserConfiguration')
		                               ->getUserConfiguration(new IdType($_SESSION['customer_id']),
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
Session ID    : {$this->getSessionId($adminData)}

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
	 * Ensure that an email key is set to session.
	 */
	private function setAdminEmailToSession()
	{
		if(!isset($_SESSION['email']))
		{
			$this->addEmailToSession();
		}
	}
	
	
	/**
	 * Runs admin log process.
	 */
	private function runAdminLogProcess()
	{
		$this->setAdminEmailToSession();
		$adminData = $this->getAdminData();
		$adminLog  = $this->createAdminLog($adminData);
		$this->logAdminActivity($adminLog);
	}
	
	
	/**
	 * Checks all conditions to log admin activity.
	 *
	 * @return bool Should logged status
	 */
	private function activityShouldBeLogged()
	{
		return $_SESSION !== null && isset($_SESSION['customer_id']) && $this->getAdminLogStatus();
	}
	
	
	/**
	 * Return array with admin data form session.
	 *
	 * @return array Admin data array.
	 */
	private function getAdminData()
	{
		return [
			'session_id'              => session_id(),
			'customers_id'            => $_SESSION['customer_id'],
			'customers_firstname'     => $_SESSION['customer_first_name'],
			'customers_lastname'      => $_SESSION['customer_last_name'],
			'customers_email_address' => $_SESSION['email'],
		];
	}
	
	
	/**
	 * Returns session id if is set else returns no session string.
	 *
	 * @param array $adminData Data array with admin data.
	 *
	 * @return string Session string id or no session string.
	 */
	private function getSessionId(array $adminData)
	{
		return isset($adminData['session_id']) ? $adminData['session_id'] : 'no session';
	}
}