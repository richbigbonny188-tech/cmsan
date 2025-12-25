<?php
/* --------------------------------------------------------------
   HermesHSIService.inc.php 2020-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

use Psr\Log\LoggerAwareInterface;

class HermesHSIService implements LoggerAwareInterface
{
    /** @var HermesHSIConfigurationStorage */
    protected $configuration;
    
    /** @var \Psr\Log\LoggerInterface */
    protected $logger;
    
    /** @var string */
    protected $lastRequest;
    
    protected const LABELSTORAGE = DIR_FS_CATALOG . '/export/hermeshsi';
    
    public function __construct(HermesHSIConfigurationStorage $configuration)
    {
        $this->configuration = $configuration;
    }
    
    
    /**
     * @return HermesHSIAccessToken
     * @throws HermesHSIAuthenticationFailedException
     * @throws RestException
     * @throws RestTimeoutException
     */
    public function getAccessToken(): HermesHSIAccessToken
    {
        try {
            $accessToken = $this->retrieveAccessTokenFromCache();
            if ($accessToken->isValid() === false) {
                $accessToken = $this->refreshToken($accessToken->getRefreshToken());
            }
            
            return $accessToken;
        } catch (HermesHSIAuthenticationRequiredException $e) {
            $accessToken = $this->authenticateUser((string)$this->configuration->get('apiUser'),
                                                   (string)$this->configuration->get('apiPassword'));
            
            return $accessToken;
        }
    }
    
    
    /**
     * @return HermesHSIAccessToken
     * @throws HermesHSIAuthenticationRequiredException
     */
    protected function retrieveAccessTokenFromCache(): HermesHSIAccessToken
    {
        $dataCache         = DataCache::get_instance();
        $cachedAccessToken = $dataCache->get_persistent_data('hermeshsi-accesstoken');
        if ($cachedAccessToken === null) {
            throw new  HermesHSIAuthenticationRequiredException('no access token in cache');
        }
        
        return $cachedAccessToken;
    }
    
    
    /**
     * Marks the contents of the AccessToken cache as dirty. Required when dis-/enabling testMode.
     *
     */
    public function markAccessTokenCachesAsDirty(): void
    {
        $this->logger->info('marking AccessToken cache as dirty');
        $dataCache         = DataCache::get_instance();
        $dataCache->write_persistent_data('hermeshsi-accesstoken', null);
    }
    
    
    /**
     * @param string $refreshToken
     *
     * @return HermesHSIAccessToken
     * @throws HermesHSIAuthenticationFailedException
     * @throws HermesHSIAuthenticationRequiredException
     */
    public function refreshToken(string $refreshToken): HermesHSIAccessToken
    {
        $this->logger->debug('refreshing token');
        if ((bool)$this->configuration->get('testMode') === true) {
            $endpointUrlBase = 'https://authme-int.myhermes.de/authorization-facade/oauth2/access_token?';
        } else {
            $endpointUrlBase = 'https://authme.myhermes.de/authorization-facade/oauth2/access_token?';
        }
        $username = $this->configuration->get('apiUser');
        $password = $this->configuration->get('apiPassword');
        $params   = [
            'client_id'     => $this->getClientId(), //$username,
            'client_secret' => $this->getClientSecret(), //$password,
            'grant_type'    => 'refresh_token',
            'refresh_token' => $refreshToken,
            'realm'         => '/',
        ];
        $url      = $endpointUrlBase . http_build_query($params, '', '&');
        
        /** @var RestRequest $restRequest */
        $restRequest = MainFactory::create('HermesHSIRestRequest', RestRequest::METHOD_POST, $url);
        
        /** @var HermesHSIRestService $restService */
        $restService = MainFactory::create('HermesHSIRestService');
        $restService->setLogger($this->logger);
        $response = $restService->performHermesHSIRequest($restRequest);
        if ($response->getResponseCode() === 200) {
            try {
                $accessTokenResponse = $response->getParsedBody();
                $accessToken         = MainFactory::create('HermesHSIAccessToken', $accessTokenResponse);
                $this->storeAccessTokenInCache($accessToken);
                $this->logger->debug("Parsed response:\n{token}", ['token' => print_r($accessTokenResponse, true)]);
                
                return $accessToken;
            } catch (HermesHSIInvalidDataException $e) {
                throw new HermesHSIAuthenticationFailedException($e->getMessage());
            }
        } elseif ($response->getResponseCode() === 400) {
            try {
                $accessTokenResponse = $response->getParsedBody();
                if (isset($accessTokenResponse['error']) && $accessTokenResponse['error'] === 'invalid_grant') {
                    throw new HermesHSIAuthenticationRequiredException($accessTokenResponse['error']);
                }
            } catch (HermesHSIInvalidDataException $e) {
                throw new HermesHSIAuthenticationFailedException($e->getMessage());
            }
        }
        
        throw new HermesHSIAuthenticationFailedException('could not authenticate');
    }
    
    
    /**
     * @param string $username
     * @param string $password
     *
     * @return HermesHSIAccessToken
     * @throws HermesHSIAuthenticationFailedException
     */
    public function authenticateUser(string $username, string $password): HermesHSIAccessToken
    {
        if ((bool)$this->configuration->get('testMode') === true) {
            $endpointUrlBase = 'https://authme-int.myhermes.de/authorization-facade/oauth2/access_token?';
        } else {
            $endpointUrlBase = 'https://authme.myhermes.de/authorization-facade/oauth2/access_token?';
        }
        $clientId     = $this->getClientId();
        $clientSecret = $this->getClientSecret();
        $params       = [
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
            'grant_type'    => 'password',
            'username'      => $username,
            'password'      => $password,
            'realm'         => '/',
        ];
        $url    = $endpointUrlBase . http_build_query($params, '', '&');
        
        /** @var RestRequest $restRequest */
        $restRequest = MainFactory::create('HermesHSIRestRequest', RestRequest::METHOD_POST, $url);
        
        /** @var HermesHSIRestService $restService */
        $restService = MainFactory::create('HermesHSIRestService');
        $restService->setLogger($this->logger);
        $response = $restService->performHermesHSIRequest($restRequest);
        //header('Content-Type: text/plain'); print_r($response); die();
        if ($response->getResponseCode() === 200) {
            try {
                $accessTokenResponse = $response->getParsedBody();
                //$accessTokenResponse['expires_in'] = 60; // todo: REMOVE THIS, testing only!
                $accessToken         = MainFactory::create('HermesHSIAccessToken', $accessTokenResponse);
                $this->storeAccessTokenInCache($accessToken);
            } catch (HermesHSIInvalidDataException $e) {
                throw new HermesHSIAuthenticationFailedException($e->getMessage());
            }
            $this->logger->debug("Parsed response:\n{token}", ['token' => print_r($accessTokenResponse, true)]);
            
            return $accessToken;
        }
        
        throw new HermesHSIAuthenticationFailedException('could not authenticate');
    }
    
    
    /**
     * @param HermesHSIShipmentOrder $shipmentOrder
     *
     * @return HermesHSIRestResponse
     * @throws HermesHSIAuthenticationFailedException
     * @throws HermesHSIInvalidDataException
     * @throws HermesHSIOrderException
     * @throws RestException
     * @throws RestTimeoutException
     */
    public function createShipmentOrder(HermesHSIShipmentOrder $shipmentOrder): string
    {
        if ((bool)$this->configuration->get('testMode') === true) {
            $endpointUrlBase = 'https://de-api-int.hermesworld.com/esb-webservice-gateway/services/rest/1/sci/v1/shipmentorders';
        } else {
            $endpointUrlBase = 'https://de-api.hermesworld.com/esb-webservice-gateway/services/rest/1/sci/v1/shipmentorders';
        }
        /** @var HermesHSIRestRequest $request */
        $request = MainFactory::create('HermesHSIRestRequest', RestRequest::METHOD_POST, $endpointUrlBase, json_encode($shipmentOrder));
        $request->setAccessToken($this->getAccessToken());
        $restService = MainFactory::create('HermesHSIRestService');
        $restService->setLogger($this->logger);
        /** @var HermesHSIRestResponse $response */
        $response = $restService->performHermesHSIRequest($request);
        if ($response->getResponseCode() === 200) {
            $parsedResponse = $response->getParsedBody();
            $shipmentOrderId = $parsedResponse['shipmentOrderID'];
            $this->lastRequest = 'POST ' . $endpointUrlBase . "\n" . json_encode($shipmentOrder);
            return $shipmentOrderId;
        }
    
        if ($response->getResponseCode() >= 400) {
            $parsedResponse = $response->getParsedBody();
            $messages = [];
            foreach ($parsedResponse['listOfResultCodes'] as $resultCode) {
                $messages[] = $resultCode['code'] . ' - ' . $resultCode['message'];
            }
            throw new HermesHSIOrderException(implode("\n", $messages));
        }
    
        throw new RestException('unhandled response');
    }
    
    
    /**
     * @param string|null $shipmentOrderId
     *
     * @return array
     * @throws HermesHSIAuthenticationFailedException
     * @throws HermesHSIInvalidDataException
     * @throws RestException
     * @throws RestTimeoutException
     */
    public function retrieveShipmentOrder(?string $shipmentOrderId = null): array
    {
        if ((bool)$this->configuration->get('testMode') === true) {
            $endpointUrlBase = 'https://de-api-int.hermesworld.com/esb-webservice-gateway/services/rest/1/sci/v1/shipmentorders?';
        } else {
            $endpointUrlBase = 'https://de-api.hermesworld.com/esb-webservice-gateway/services/rest/1/sci/v1/shipmentorders?';
        }
        $params = [];
        $params['shipmentOrderID'] = $shipmentOrderId;
        $params['shipmentOrderState'] = ''; // CREATED|DELETED|PLACED|ARCHIVED|FAILED
        if (empty($shipmentOrderId)) {
            $params['shipmentOrderStartDate'] = (new DateTime('2 days ago'))->format('Y-m-d');
            $params['shipmentOrderEndDate'] = (new DateTime('today'))->format('Y-m-d');
        }
        $params = array_filter($params);
        $url = $endpointUrlBase . http_build_query($params, '', '&');
        /** @var HermesHSIRestRequest $request */
        $request = MainFactory::create('HermesHSIRestRequest', RestRequest::METHOD_GET, $url);
        $request->setAccessToken($this->getAccessToken());
        $restService = MainFactory::create('HermesHSIRestService');
        $restService->setLogger($this->logger);
        /** @var HermesHSIRestResponse $response */
        $response = $restService->performHermesHSIRequest($request);
        if ($response->getResponseCode() === 200) {
            $parsedResponse = $response->getParsedBody();
            return $parsedResponse;
        }
        throw new RestException('unhandled response');
    }
    
    public function retrieveShipmentOrders(array $shipmentOrderIds): array
    {
        if ((bool)$this->configuration->get('testMode') === true) {
            $endpointUrlBase = 'https://de-api-int.hermesworld.com/esb-webservice-gateway/services/rest/1/sci/v1/shipmentorders?';
        } else {
            $endpointUrlBase = 'https://de-api.hermesworld.com/esb-webservice-gateway/services/rest/1/sci/v1/shipmentorders?';
        }
        $url = $endpointUrlBase;
        foreach ($shipmentOrderIds as $shipmentOrderId) {
            $shipmentOrderId = (string)$shipmentOrderId;
            if (preg_match('/^\d+$/', $shipmentOrderId) !== 1) {
                continue;
            }
            $url .= 'shipmentOrderID=' . $shipmentOrderId . '&';
        }
        
        /** @var HermesHSIRestRequest $request */
        $request = MainFactory::create('HermesHSIRestRequest', RestRequest::METHOD_GET, $url);
        $request->setAccessToken($this->getAccessToken());
        $restService = MainFactory::create('HermesHSIRestService');
        $restService->setLogger($this->logger);
        /** @var HermesHSIRestResponse $response */
        $response = $restService->performHermesHSIRequest($request);
        if ($response->getResponseCode() === 200) {
            $parsedResponse = $response->getParsedBody();
            
            $shipmentOrders = [];
            foreach ($parsedResponse['listOfClients'][0]['listOfSenders'][0]['listOfShipmentOrders'] as $shipmentOrder) {
                $shipmentOrders[$shipmentOrder['shipmentOrderID']] = $shipmentOrder;
            }
            
            return $shipmentOrders;
        }
        throw new RestException('unhandled response');
    }
    
    
    /**
     * @param string $shipmentOrderId
     *
     * @return HermesHSILabel
     * @throws HermesHSIAuthenticationFailedException
     * @throws HermesHSIInvalidDataException
     * @throws HermesHSILabelException
     * @throws RestException
     * @throws RestTimeoutException
     */
    public function getLabel(string $shipmentOrderId): HermesHSILabel
    {
        // check storage
        $fileHash = hash('sha256', $shipmentOrderId . $this->configuration->get('apiUser'));
        $fileName = $shipmentOrderId . '_' . $fileHash . '.pdf';
        if (file_exists(static::LABELSTORAGE . '/' . $fileName)) {
            $this->logger->debug('using label for ' . $shipmentOrderId . ' from local storage');
            $labelData = file_get_contents(static::LABELSTORAGE . '/' . $fileName);
            /** @var HermesHSILabel $hermesLabel */
            $hermesLabel = MainFactory::create('HermesHSILabel', $labelData);
            $hermesLabel->setFromLocalStorage(true);
            return $hermesLabel;
        }
    
        // not in storage, try to create it via HSI
        if (!is_dir(static::LABELSTORAGE) && !mkdir($concurrentDirectory = static::LABELSTORAGE)
            && !is_dir($concurrentDirectory)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
            }
        $this->logger->debug('creating label for ' . $shipmentOrderId);
        $hermesLabel = $this->retrieveLabel($shipmentOrderId);
        file_put_contents(static::LABELSTORAGE . '/' . $fileName, $hermesLabel->getLabelData());
        return $hermesLabel;
    }
    
    /**
     * @param string $shipmentOrderId
     *
     * @return HermesHSILabel
     * @throws HermesHSIAuthenticationFailedException
     * @throws HermesHSIInvalidDataException
     * @throws HermesHSILabelException
     * @throws RestException
     * @throws RestTimeoutException
     */
    public function retrieveLabel(string $shipmentOrderId): HermesHSILabel
    {
        if ((bool)$this->configuration->get('testMode') === true) {
            $endpointUrlBase = 'https://de-api-int.hermesworld.com/esb-webservice-gateway/services/rest/1/sci/v1/shipmentorders/' . $shipmentOrderId . '/labels/';
        } else {
            $endpointUrlBase = 'https://de-api.hermesworld.com/esb-webservice-gateway/services/rest/1/sci/v1/shipmentorders/' . $shipmentOrderId . '/labels/';
        }
        $requestParams = [
            'shipmentOrderID' => $shipmentOrderId,
        ];
        /** @var HermesHSIRestRequest $request */
        $request = MainFactory::create('HermesHSIRestRequest', RestRequest::METHOD_POST, $endpointUrlBase, json_encode($requestParams));
        $request->setAccessToken($this->getAccessToken());
        $restService = MainFactory::create('HermesHSIRestService');
        $restService->setLogger($this->logger);
        /** @var HermesHSIRestResponse $response */
        $response = $restService->performHermesHSIRequest($request);
        if ($response->getResponseCode() === 200) {
            $parsedResponse = $response->getParsedBody();
            $labelData = base64_decode($parsedResponse['labelImage']);
            $shipmentId = $parsedResponse['shipmentID'];
            $hermesHSILabel = MainFactory::create('HermesHSILabel', $labelData, $shipmentId);
            return $hermesHSILabel;
        }

        if ($response->getResponseCode() >= 400) {
            $parsedResponse = $response->getParsedBody();
            $messages = [];
            foreach ($parsedResponse['listOfResultCodes'] as $resultCode) {
                $messages[] = $resultCode['code'] . ' - ' . $resultCode['message'];
            }
            throw new HermesHSILabelException(implode("\n", $messages));
        }
    
        throw new RestException('unhandled response');
    }
    
    
    /**
     * @param HermesHSIAccessToken $accessToken
     */
    protected function storeAccessTokenInCache(HermesHSIAccessToken $accessToken): void
    {
        $dataCache = DataCache::get_instance();
        $dataCache->write_persistent_data('hermeshsi-accesstoken', $accessToken);
    }
    
    
    /**
     * Sets a logger instance on the object
     *
     * @param \Psr\Log\LoggerInterface $logger
     *
     * @return null
     */
    public function setLogger(\Psr\Log\LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    
    
    /**
     * @return string
     */
    public function getLastRequest(): string
    {
        return $this->lastRequest;
    }
    
    protected function getClientId(): string
    {
        if ((bool)$this->configuration->get('testMode') === true) {
            $clientId = base64_decode('aHNpLmludC52ZXJtLkdhbWJpbw==');
        } else {
            $clientId = base64_decode('aHNpLnZlcm0uR2FtYmlv');
        }
        return $clientId;
    }

    protected function getClientSecret(): string
    {
        if ((bool)$this->configuration->get('testMode') === true) {
            $secret = base64_decode('Z09SNyYkUXlzSiM=');
        } else {
            $secret = base64_decode('UjNoSlNhNWpDTDlOSitH');
        }
        return $secret;
    }
    
}
