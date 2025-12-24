<?php
/* --------------------------------------------------------------
   RequestControl.php 2018-08-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\AdminFeed;

use Exception;
use Gambio\AdminFeed\Adapters\GxAdapterTrait;

/**
 * Class RequestControl
 *
 * @package Gambio\AdminFeed\Services\ShopInformation
 */
class RequestControl
{
    use GxAdapterTrait;
    
    /**
     * @var string
     */
    private $allowedIpsUrl = 'https://core-api.gambiohub.com/trust/allowed_admin_feed_ips.json';
    
    /**
     * @var string
     */
    private $tokenDataCacheKey = 'admin-feed-request-tokens';
    
    /**
     * @var int
     */
    private $tokenLifeSpan = 300; # 5 minutes
    
    /**
     * @var CurlClient
     */
    private $curl;
    
    
    /**
     * RequestControl constructor.
     *
     * @param CurlClient $curl
     */
    public function __construct(CurlClient $curl)
    {
        $this->curl = $curl;
    }
    
    
    /**
     * Creates a new request token and returns it.
     *
     * @return string
     */
    public function createRequestToken()
    {
        $this->deleteOldRequestTokens();
        
        $token     = uniqid();
        $tokenData = [
            [
                'timestamp' => time(),
                'token'     => $token,
            ]
        ];
        
        $dataCache = $this->gxAdapter()->getDataCache();
        if ($dataCache->key_exists($this->tokenDataCacheKey, true)) {
            $dataCache->add_data($this->tokenDataCacheKey, $tokenData, true);
        } else {
            $dataCache->set_data($this->tokenDataCacheKey, $tokenData, true);
        }
        
        return $token;
    }
    
    
    /**
     * Checks a given token and tries to verify it.
     *
     * @param string $token
     *
     * @return bool Returns true, if the given token is valid, otherwise false will be returned.
     */
    public function verifyRequestToken($token)
    {
        $availableTokens = $this->getRequestTokens();
        
        return in_array($token, $availableTokens);
    }
    
    
    /**
     * Checks a given ip and tries to verify it.
     *
     * @param string $ip
     *
     * @return bool Returns true, if the given ip is valid, otherwise false will be returned.
     */
    public function verifyRequestIp($ip)
    {
        try {
            $this->curl->executeGet($this->allowedIpsUrl);
            
            if ($this->curl->getStatusCode() !== 200) {
                return false;
            }
            
            $ipList = @json_decode($this->curl->getContent(), true);
            if (is_array($ipList)) {
                $valid = false;
                foreach ($ipList as $allowedIp) {
                    if ($allowedIp === '*' || $allowedIp === $ip
                        || (strpos($allowedIp, '*') !== false
                            && strpos($ip, substr($allowedIp, 0, strpos($allowedIp, '*'))) === 0)) {
                        $valid = true;
                        break;
                    }
                }
                
                return $valid;
            }
            
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    
    
    /**
     * Returns all active and existing request tokens.
     *
     * @return array
     */
    private function getRequestTokens()
    {
        $this->deleteOldRequestTokens();
        
        $dataCache = $this->gxAdapter()->getDataCache();
        if (!$dataCache->key_exists($this->tokenDataCacheKey, true)) {
            return [];
        }
        
        $token      = [];
        $tokensData = $dataCache->get_data($this->tokenDataCacheKey, true);
        if (is_array($tokensData) && count($tokensData) > 0) {
            foreach ($tokensData as $tokenData) {
                $token[] = $tokenData['token'];
            }
        }
        
        return $token;
    }
    
    
    /**
     * Deletes old request tokens.
     *
     * @return void
     */
    private function deleteOldRequestTokens()
    {
        $dataCache = $this->gxAdapter()->getDataCache();
        if (!$dataCache->key_exists($this->tokenDataCacheKey, true)) {
            return;
        }
        
        $tokensData = $dataCache->get_data($this->tokenDataCacheKey, true);
        if (is_array($tokensData) && count($tokensData) > 0) {
            foreach ($tokensData as $index => $tokenData) {
                if ($tokenData['timestamp'] < time() - $this->tokenLifeSpan) {
                    unset($tokensData[$index]);
                }
            }
        }
        
        $dataCache->set_data($this->tokenDataCacheKey, $tokensData, true);
    }
}