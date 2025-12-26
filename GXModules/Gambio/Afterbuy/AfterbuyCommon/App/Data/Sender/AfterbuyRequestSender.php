<?php
/* --------------------------------------------------------------
   AfterbuyRequestSender.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\AfterbuyCommon\App\Data\Sender;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\App\AfterbuyLogger;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\Model\AfterbuyXmlRequest;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\Model\OrderId;
use GXModules\Gambio\Afterbuy\OrderExport\Exceptions\AfterbuyResponseException;

/**
 * Class AfterbuyRequestSender
 *
 * @package GXModules\Gambio\Afterbuy\OrderExport\App\Data\AfterbuySender
 */
class AfterbuyRequestSender
{
    private const HTTP_URL = 'https://api.afterbuy.de/afterbuy/ABInterface.aspx';
    
    
    private const HTTP_CONTENT_TYPE = 'application/xml';
    
    
    private const REQUEST_TIMEOUT = 10;
    
    
    /**
     * @var Client
     */
    private Client $client;
    
    
    /**
     * @var AfterbuyXmlResponseValidator
     */
    private AfterbuyXmlResponseValidator $responseValidator;
    
    
    /**
     * @var AfterbuyLogger
     */
    private AfterbuyLogger $logger;
    
    
    /**
     * AfterbuyRequestSender constructor.
     *
     * @param Client                       $client
     * @param AfterbuyXmlResponseValidator $responseValidator
     * @param AfterbuyLogger               $logger
     */
    public function __construct(Client $client, AfterbuyXmlResponseValidator $responseValidator, AfterbuyLogger $logger)
    {
        $this->client            = $client;
        $this->responseValidator = $responseValidator;
        $this->logger            = $logger;
    }
    
    
    /**
     * Sends the request to the Afterbuy-API.
     * This method also validates the Afterbuy-Response and throws an exception in case of an error.
     * All details are logged in the validator.
     *
     * @param AfterbuyXmlRequest $request
     * @param OrderId|null       $orderId
     *
     * @return array{status: int, headers: string[][], body: string}
     * @throws AfterbuyResponseException
     */
    public function send(AfterbuyXmlRequest $request, OrderId $orderId = null): array
    {
        $requestOptions = [
            RequestOptions::BODY    => $request->toXmlString(),
            RequestOptions::HEADERS => [
                'Content-Type' => self::HTTP_CONTENT_TYPE,
            ],
            RequestOptions::TIMEOUT => self::REQUEST_TIMEOUT,
        ];
        
        $this->logRequest($request, $orderId);
        $response = $this->client->post(self::HTTP_URL, $requestOptions);
        
        $responseBody = $response->getBody()->getContents();
        $this->logResponse($request, $responseBody, $orderId);
        $this->responseValidator->validate($responseBody);
        
        return [
            'status'  => $response->getStatusCode(),
            'headers' => $response->getHeaders(),
            'body'    => $responseBody,
        ];
    }
    
    
    /**
     * Logs the request to the Afterbuy XML-API.
     *
     * @param AfterbuyXmlRequest $request
     * @param OrderId|null       $orderId
     */
    private function logRequest(AfterbuyXmlRequest $request, OrderId $orderId = null): void
    {
        $message = "Sending request to Afterbuy XML-API";
        $context = [
            'requestBody' => $request->toXmlString(),
        ];
        if ($orderId) {
            $context['orderId'] = $orderId->orderId();
            $message            .= " (Order ID: {$orderId->asString()})";
        }
        
        $this->logger->notice($message, $context);
    }
    
    
    /**
     * Logs the response from the Afterbuy XML-API.
     *
     * @param AfterbuyXmlRequest $request
     * @param string             $responseBody
     * @param OrderId|null       $orderId
     *
     * @return void
     */
    private function logResponse(AfterbuyXmlRequest $request, string $responseBody, OrderId $orderId = null): void
    {
        $message = "Received response from Afterbuy XML-API";
        $context = [
            'requestBody'  => $request->toXmlString(),
            'responseBody' => $responseBody,
        ];
        if ($orderId) {
            $context['orderId'] = $orderId->orderId();
            $message            .= " (Order ID: {$orderId->asString()})";
        }
        
        $this->logger->notice($message, $context);
    }
}