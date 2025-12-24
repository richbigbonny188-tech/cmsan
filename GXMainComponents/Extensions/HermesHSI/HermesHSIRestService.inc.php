<?php
/* --------------------------------------------------------------
   HermesHSIRestService.inc.php 2019-10-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

class HermesHSIRestService extends RestService implements LoggerAwareInterface
{
    protected $logger;
    
    
    /**
     * @param RestRequest $request
     *
     * @return RestCurlResponse
     * @throws RestException
     * @throws RestTimeoutException
     */
    public function performRequest(RestRequest $request)
    {
        $this->logger->debug('Request for URL {url}', ['url' => $request->getURL()]);
        $response = parent::performRequest($request);
        $this->logger->debug("Response is {code}\n{body}",
                             ['code' => $response->getResponseCode(), 'body' => $response->getResponseBody()]);
        
        return $response;
    }
    
    
    public function performHermesHSIRequest(HermesHSIRestRequest $request)
    {
        $this->logger->debug("Request (HSI) for URL {url} with body\n{body}",
                             ['url' => $request->getURL(), 'body' => print_r($request->getData(), true)]);
        $response = parent::performRequest($request);
        $this->logger->debug("Response is {code}\n--BODY--\n{body}\n--END_BODY--",
                             ['code' => $response->getResponseCode(), 'body' => $response->getResponseBody()]);
        /** @var HermesHSIRestResponse $hermesHSIRestResponse */
        $hermesHSIRestResponse = MainFactory::create('HermesHSIRestResponse',
                                                     $response->getResponseCode(),
                                                     $response->getResponseBody());
        $hermesHSIRestResponse->setResponseHeaders($response->getResponseHeaders(null));
        
        return $hermesHSIRestResponse;
    }
    
    
    /**
     * Sets a logger instance on the object
     *
     * @param LoggerInterface $logger
     *
     * @return null
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
