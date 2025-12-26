<?php
/*--------------------------------------------------------------------------------------------------
    ApiClient.php 2021-04-08
    swisswebXperts GmbH
    https://www.swisswebxperts.ch
    Copyright (c) 2021 swisswebXperts GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace SwixPostfinanceCheckout;

use Gambio\Core\Logging\LoggerBuilder;
use SwixPostfinanceCheckout\Service\RefundService;
use SwixPostfinanceCheckout\Service\SpaceService;
use SwixPostfinanceCheckout\Service\WebhookListenerService;
use SwixPostfinanceCheckout\Service\WebhookUrlService;
use SwixPostfinanceCheckout\Service\TransactionService;
use SwixPostfinanceCheckout\Service\TransactionPaymentPageService;

class ApiClient
{
    protected $basePath = 'https://checkout.postfinance.ch:443/api';

    protected $userId;
    protected $authenticationKey;
    protected $macVersion = 1;

    protected $spaceService = null;
    protected $webhookListenerService = null;
    protected $webhookUrlService = null;
    protected $transactionService = null;
    protected $transactionPaymentPageService = null;
    protected $refundService = null;
    protected $devMode = false;
    
    public function __construct($userId, $authenticationKey)
    {
        $this->userId = $userId;
        $this->authenticationKey = $authenticationKey;
    
        $this->devMode = file_exists(DIR_FS_CATALOG . '/.dev-environment');
    }

    public function getSpaceService()
    {
        if (is_null($this->spaceService)) {
            $this->spaceService = new SpaceService($this);
        }

        return $this->spaceService;
    }

    public function getWebhookListenerService()
    {
        if (is_null($this->webhookListenerService)) {
            $this->webhookListenerService = new WebhookListenerService($this);
        }

        return $this->webhookListenerService;
    }

    public function getWebhookUrlService()
    {
        if (is_null($this->webhookUrlService)) {
            $this->webhookUrlService = new WebhookUrlService($this);
        }

        return $this->webhookUrlService;
    }

    public function getTransactionService()
    {
        if (is_null($this->transactionService)) {
            $this->transactionService = new TransactionService($this);
        }

        return $this->transactionService;
    }

    public function getTransactionPaymentPageService()
    {
        if (is_null($this->transactionPaymentPageService)) {
            $this->transactionPaymentPageService = new TransactionPaymentPageService($this);
        }

        return $this->transactionPaymentPageService;
    }

    public function getRefundService()
    {
        if (is_null($this->refundService)) {
            $this->refundService = new RefundService($this);
        }

        return $this->refundService;
    }

    public function call($resource, $method, $queryParams = null, $postData = null)
    {
        $path = $resource;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_DNS_CACHE_TIMEOUT, 30);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

        curl_setopt($curl, CURLOPT_VERBOSE, 1);

        if (is_array($queryParams)) {
            $path .= $this->getQueryString($queryParams);
        }

        $body = '';
        if ($method === 'POST') {
            $body = $this->getBody($postData);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        }

        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getHeaders($method, $path));

        curl_setopt($curl, CURLOPT_URL, $this->basePath . $path);
        curl_setopt($curl, CURLOPT_VERBOSE, 1);
    
        $this->debugLog("Sending {$method} to {$path} with {$body}");
        
        $response = curl_exec($curl);
        $curlInfo = curl_getinfo($curl);

        curl_close($curl);
    
        $this->debugLog("Response ({$curlInfo['http_code']}) body:\n{$response}");
        
        if (isset($curlInfo['http_code']) && $curlInfo['http_code'] === 200) {
            if ($curlInfo['content_type'] === 'application/json;charset=utf-8') {
                return json_decode($response, true);
            }
            return $response;
        }

        return false;
    }
    
    protected function debugLog($message)
    {
        if (!$this->devMode) {
            return;
        }
        /** @var LoggerBuilder $loggerBuilder */
        $loggerBuilder = \LegacyDependencyContainer::getInstance()->get(LoggerBuilder::class);
        $logger  = $loggerBuilder->omitRequestData()->changeNamespace('swixpostfinancecheckout')->build();
        $logger->debug($message);
    }

    protected function getQueryString($data)
    {
        $parts = [];
        foreach($data as $key => $value) {
            $parts[] = $key . '=' . $value;
        }

        return '?' . implode('&', $parts);
    }

    protected function getBody($postData)
    {
        return json_encode($postData);
    }

    protected function getHeaders($method, $path)
    {
        $timestamp = time();
        $headers = [];
        $headers['x-mac-version'] = $this->macVersion;
        $headers['x-mac-userid'] = $this->userId;
        $headers['x-mac-timestamp'] = $timestamp;
        $headers['x-mac-value'] = $this->getMacValue($method, $path, $timestamp);

        if ($method == 'POST') {
            $headers['Content-Type'] = 'application/json';
            $headers['Accept'] = 'application/json';
        }

        $outputHeaders = [];
        foreach($headers as $name => $value) {
            $outputHeaders[] = strtolower($name) . ': ' . $value;
        }

        return $outputHeaders;
    }

    protected function getMacValue($method, $path, $timestamp)
    {
        $parts = [];
        $parts[] = $this->macVersion;
        $parts[] = $this->userId;
        $parts[] = $timestamp;
        $parts[] = $method;
        $parts[] = '/api' . $path;

        $decodedSecret = base64_decode($this->authenticationKey);

        return base64_encode(hash_hmac("sha512", implode('|', $parts), $decodedSecret, true));
    }
}