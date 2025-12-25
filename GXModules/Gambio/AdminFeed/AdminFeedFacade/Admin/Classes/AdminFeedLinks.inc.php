<?php
/* --------------------------------------------------------------
   AdminFeedLinks.inc.php 2018-07-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AdminFeedLinks
 *
 * Provides several admin feed URLs
 */
class AdminFeedLinks
{
    /**
     * @const string ADMIN_NEWS_URL URL for the admin news
     */
    const ADMIN_NEWS_URL = 'https://news.gambio-support.de/news.php?category=gx4';
    
    /**
     * @const string SERVER_INFO_SEND_URL URL to send the server info to
     */
    const SERVER_INFO_SEND_URL = 'https://www.gambio-support.de/misc/serverinfo/';
    
    /**
     * @const string DYNAMIC_SHOP_MESSAGES_URL URL to fetch dynamic shop messages
     */
    const DYNAMIC_SHOP_MESSAGES_URL = 'https://www.gambio-support.de/updateinfo/';
    
    /**
     * @const string SHOP_MESSAGES URL to fetch shop messages
     */
    const SHOP_MESSAGES = 'https://www.gambio-support.de/version_news';
    
    /**
     * @const string AUTO_UPDATER_URL URL to fetch auto updater updates
     */
    const AUTO_UPDATER_URL = 'https://updates.gambio-support.de/v2/check.php';
    
    
    public static function asArray()
    {
        $selfReflection = new ReflectionClass(__CLASS__);
        
        return $selfReflection->getConstants();
    }
}