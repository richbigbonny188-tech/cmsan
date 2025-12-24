<?php
/* --------------------------------------------------------------
   xtc_check_agent.inc.php 2022-11-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(html_output.php,v 1.52 2003/03/19); www.oscommerce.com
   (c) 2003     nextcommerce (xtc_href_link.inc.php,v 1.3 2003/08/13); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_check_agent.inc.php 974 2005-06-07 12:40:29Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

use Jaybizzle\CrawlerDetect\CrawlerDetect;

function xtc_check_agent()
{
	static $isRobot;
	
	if($isRobot !== null)
	{
		return $isRobot;
	}
	
	$isRobot = 0;

	if(defined('CHECK_CLIENT_AGENT') && CHECK_CLIENT_AGENT === 'true')
	{
        $isRobot = (new CrawlerDetect)->isCrawler() ? 1 : 0;
	}

	return $isRobot;
}
