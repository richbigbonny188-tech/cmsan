<?php
/* --------------------------------------------------------------
   ExternalLinks.inc.php 2018-07-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ExternalLinks
 *
 * Provides several external URLs
 */
class ExternalLinks
{
	/**
	 * @const string ADMIN_NEWS_URL URL for the admin news
	 * @deprecated
	 */
	const ADMIN_NEWS_URL = 'https://news.gambio-support.de/news.php?category=gx4';
	
	/**
	 * @const string SHOP_KEY_REQUEST_URL URL to get a shop key from
	 */
	const SHOP_KEY_REQUEST_URL = 'http://www.gambio.de/0n7hb';
	
	/**
	 * @const string SERVER_INFO_SEND_URL URL to send the server info to
	 * @deprecated
	 */
	const SERVER_INFO_SEND_URL = 'https://www.gambio-support.de/misc/serverinfo/';
	
	/**
	 * @const string DYNAMIC_SHOP_MESSAGES_URL URL to fetch dynamic shop messages
	 * @deprecated
	 */
	const DYNAMIC_SHOP_MESSAGES_URL = 'https://www.gambio-support.de/updateinfo/';

    /**
     * @const string SHOP_MESSAGES URL to fetch shop messages
     * @deprecated
     */
	const SHOP_MESSAGES = 'https://www.gambio-support.de/version_news';

	
	public static function asArray()
	{
		$selfReflection = new ReflectionClass(__CLASS__);
		return $selfReflection->getConstants();
	}
}