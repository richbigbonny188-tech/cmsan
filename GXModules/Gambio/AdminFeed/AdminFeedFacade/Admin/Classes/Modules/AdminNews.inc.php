<?php
/* --------------------------------------------------------------
   AdminNews.inc.php 2018-08-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use Gambio\AdminFeed\CurlClient;
use Gambio\AdminFeed\RequestControl;

/**
 * Class ExternalSnippets
 */
class AdminNews
{
    /**
     * @var string
     */
    protected $shopVersion;
    
    /**
     * @var string URL to fetch dynamic shop messages
     */
    protected $endpointUrl;
    
    
    /**
     * AdminNews constructor.
     */
    public function __construct()
    {
        include DIR_FS_CATALOG . 'release_info.php';
        $this->shopVersion = $gx_version;
        $this->endpointUrl = AdminFeedLinks::ADMIN_NEWS_URL;
    }
    
    
    /**
     * Returns the news as html code.
     *
     * @return string
     */
    public function news()
    {
        $return = 'Timeout';
        
        $adminFeedToken = '';
        if (gm_get_conf('ADMIN_FEED_ACCEPTED_SHOP_INFORMATION_DATA_PROCESSING') === 'true') {
            $requestControl = new RequestControl(new CurlClient());
            $adminFeedToken = $requestControl->createRequestToken();
        }
        
        $coo_load_url = MainFactory::create_object('LoadUrl');
        $t_result     = $coo_load_url->load_url($this->endpointUrl . '&get_news_for_version='
                                                . rawurlencode($this->shopVersion) . '&adminFeedToken='
                                                . rawurlencode($adminFeedToken) . '&shop_url='
                                                . rawurlencode(HTTP_SERVER . DIR_WS_CATALOG));
        
        if ($t_result) {
            /**
             * @var UserConfigurationService $userConfiguration
             */
            $userConfigurationService = StaticGXCoreLoader::getService('UserConfiguration');
            $userId                   = new IdType((int)$_SESSION['customer_id']);
            
            preg_match('/<!--\s+news_content_stamp:\s*([\d]{4}-[\d]{2}-[\d]{2} [\d]{2}:[\d]{2})\s+-->/',
                       $t_result,
                       $matches);
            
            if (isset($matches[1])) {
                if ($userConfigurationService->getUserConfiguration($userId, 'news_content_stamp') !== $matches[1]) {
                    $userConfigurationService->setUserConfiguration($userId, 'news_content_stamp', $matches[1]);
                    $userConfigurationService->setUserConfiguration($userId, 'dashboard_chart_collapse', 'true');
                }
            }
            
            $return = $t_result;
        }
        
        return $return;
    }
}