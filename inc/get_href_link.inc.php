<?php
/* --------------------------------------------------------------
   get_href_link.inc.php 2020-06-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once DIR_FS_INC . 'clean_param.inc.php';

/**
 * Returns a url.
 *
 * @param string $httpServer
 * @param string $httpsServer
 * @param string $baseDir
 * @param bool   $isSslEnabled
 * @param string $page
 * @param string $queryString
 * @param string $connection
 * @param bool   $allowSessionIdInUrl
 * @param bool   $deprecatedXtcSeoUrl
 * @param bool   $relativeUrl
 * @param bool   $encodeAmpersand
 * @param bool   $suppressLanguageCode
 *
 * @return string
 */
function get_href_link($httpServer,
                       $httpsServer,
                       $baseDir,
                       $isSslEnabled,
                       $page = '',
                       $queryString = '',
                       $connection = 'NONSSL',
                       $allowSessionIdInUrl = true,
                       $deprecatedXtcSeoUrl = true,
                       $relativeUrl = false,
                       $encodeAmpersand = true,
                       $suppressLanguageCode = false)
{
	static $searchKeysForcingSslUrl;
	
	// first call: initialize static vars
    $searchKeysForcingSslUrl = $searchKeysForcingSslUrl === null ? [] : $searchKeysForcingSslUrl;
	
	// build array of search keys which are a criterion for building a SSL url
	if(count($searchKeysForcingSslUrl) === 0 && function_exists('xtc_db_query') && function_exists('gm_get_env_info'))
	{
        $seoBoost = MainFactory::create_object('GMSEOBoost', [], true);

        $callbackUrl = $seoBoost->get_boosted_content_url($seoBoost->get_content_id_by_content_group(14),
                                                          $_SESSION['languages_id']);
        if (is_string($callbackUrl)) {
            $searchKeysForcingSslUrl[] = $callbackUrl;
        }

        $contactUrl = $seoBoost->get_boosted_content_url($seoBoost->get_content_id_by_content_group(7),
                                                         $_SESSION['languages_id']);
        if (is_string($contactUrl)) {
            $searchKeysForcingSslUrl[] = $contactUrl;
        }

		$searchKeysForcingSslUrl[] = 'coID=14'; // callback
		$searchKeysForcingSslUrl[] = 'coID=7'; // contact
		
		$searchKeysForcingSslUrl[] = 'newsletter.php';
		$searchKeysForcingSslUrl[] = 'gm_price_offer.php';
		$searchKeysForcingSslUrl[] = 'product_reviews_write.php';
	}
	
	foreach($searchKeysForcingSslUrl as $searchKey)
	{
		if($connection === 'NONSSL'
		   && (strpos($queryString, $searchKey) !== false || strpos($page, $searchKey) !== false)
		   && strpos(gm_get_env_info('SCRIPT_NAME'), '/admin/') === false
		)
		{
			// force SSL
			$connection = 'SSL';
		}
	}
	
	if(!is_string($page))
	{
		$page = FILENAME_DEFAULT;
	}
	
	$url = $httpServer . $baseDir;
	
	if($relativeUrl === true)
	{
		$url = '';
	}
	elseif($connection === 'SSL' && $isSslEnabled)
	{
		$url = $httpsServer . $baseDir;
	}
    
    if ($page !== FILENAME_DEFAULT
        && APPLICATION_RUN_MODE === 'frontend'
        && !$suppressLanguageCode
        && gm_get_conf('USE_SEO_BOOST_LANGUAGE_CODE') === 'true'
        && SecurityCheck::getHtaccessVersion() >= 2.1) {
        if (strpos($page, $_SESSION['language_code'] . '/') === 0) {
            $page = substr($page, 3);
        }
        
        $url .= $_SESSION['language_code'] . '/'; // ???
    }

	$url .= $page;
	
	$queryString = clean_param($queryString, false, $encodeAmpersand);
	if(is_string($queryString) && $queryString !== '')
	{
		$url .= '?' . $queryString;
	}
	
	if(defined('SEARCH_ENGINE_FRIENDLY_URLS') && SEARCH_ENGINE_FRIENDLY_URLS === 'true' && $deprecatedXtcSeoUrl)
	{
		$url = str_replace(array('?', '&amp;', '&', '='), '/', $url);
	}

	return $url;
}
