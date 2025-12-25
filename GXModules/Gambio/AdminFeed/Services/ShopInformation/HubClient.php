<?php
/* --------------------------------------------------------------
   HubClient.php 2018-08-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\AdminFeed\Services\ShopInformation;

use AuthHashCreator;
use Exception;
use Gambio\AdminFeed\Adapters\GxAdapter;
use Gambio\AdminFeed\CurlClient;
use HubPublic\Exceptions\CurlRequestException;
use HubSessionsApiClient;
use UnexpectedValueException;

/**
 * Class HubClient
 *
 * @package Gambio\AdminFeed\Services\ShopInformation
 */
class HubClient
{
    /**
     * @var Settings
     */
    private $settings;
    
    /**
     * @var GxAdapter
     */
    private $gxAdapter;
    
    /**
     * @var CurlClient
     */
    private $curl;
    
    
    /**
     * HubClient constructor.
     *
     * @param Settings   $settings
     * @param GxAdapter  $gxAdapter
     * @param CurlClient $curl
     */
    public function __construct(Settings $settings, GxAdapter $gxAdapter, CurlClient $curl)
    {
        $this->settings  = $settings;
        $this->gxAdapter = $gxAdapter;
        $this->curl      = $curl;
    }
    
    
    /**
     * Returns the hub modules data.
     *
     * @return array
     */
    public function getHubModulesData()
    {
        try {
            $hubClientKey = $this->settings->getHubClientKey();
            if (empty($hubClientKey)) {
                return [];
            }
            
            $hubSessionKey = $this->startHubSession();
            if (empty($hubSessionKey)) {
                return [];
            }
            
            $url = $this->settings->getGambioHubConfigUrl() . '/clients/' . $hubClientKey . '/sessions/'
                   . $hubSessionKey . '/payment_modules?language=en';
            $this->curl->executeGet($url);
            
            if ($this->curl->getStatusCode() !== 200) {
                return [];
            }
            $modulesData = json_decode($this->curl->getContent(), true);
        } catch (Exception $e) {
            return [];
        }
        
        return $modulesData;
    }
    
    
    /**
     * @return string
     */
    private function startHubSession()
    {
        $languageCode              = $this->gxAdapter->getCurrentLanguageCode();
        $hubServiceFactory         = $this->gxAdapter->mainFactoryCreate('HubServiceFactory');
        $hubSessionKeyService      = $hubServiceFactory->createHubSessionKeyService();
        $hubClientKeyConfiguration = $this->gxAdapter->mainFactoryCreate('HubClientKeyConfiguration');
        $curlRequest               = $this->gxAdapter->getHubCurlRequest();
        $logControl                = $this->gxAdapter->getLogControl();
        $hubSettings               = $this->gxAdapter->mainFactoryCreate('HubSettings',
                                                                         $this->settings->getGambioHubCurlTimeout());
        
        /** @var HubSessionsApiClient $hubSessionsApiClient */
        $hubSessionsApiClient = $this->gxAdapter->mainFactoryCreate('HubSessionsApiClient',
                                                                    $this->settings->getGambioHubUrl(),
                                                                    $hubSessionKeyService,
                                                                    $hubClientKeyConfiguration,
                                                                    $curlRequest,
                                                                    $logControl,
                                                                    $hubSettings);
        
        $authHash = AuthHashCreator::create();
        
        try {
            return $hubSessionsApiClient->startSession($authHash,
                                                       $this->settings->getHttpServer()
                                                       . $this->settings->getShopDirectory(),
                                                       $languageCode);
        } catch (UnexpectedValueException $e) {
            AuthHashCreator::invalidate($authHash);
        } catch (CurlRequestException $e) {
            AuthHashCreator::invalidate($authHash);
        }
        
        return '';
    }
}