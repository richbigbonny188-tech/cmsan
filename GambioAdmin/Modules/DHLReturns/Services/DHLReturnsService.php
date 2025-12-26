<?php
/* --------------------------------------------------------------
   DHLReturnsService.php 2022-02-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\DHLReturns\Services;

use Gambio\Admin\Modules\DHLReturns\App\Data\ReturnOrderMapper;
use Gambio\Admin\Modules\DHLReturns\Model\ValueObjects\ReturnOrder;
use Gambio\Admin\Modules\DHLReturns\Model\ValueObjects\ReturnOrderConfirmation;
use GuzzleHttp\Client;
use function Gambio\Core\Logging\logger;

class DHLReturnsService
{
    private const LOGGING_NAMESPACE = 'dhlreturns';
    
    /**
     * @var Client
     */
    private $client;
    /**
     * @var DHLConfigurationService
     */
    private $configurationService;
    
    
    /**
     * DHLReturnsService constructor.
     */
    public function __construct(Client $client, DHLConfigurationService $configurationService)
    {
        $this->client               = $client;
        $this->configurationService = $configurationService;
    }
    
    
    public function createReturnLabel(ReturnOrder $returnOrder): ReturnOrderConfirmation
    {
        logger(static::LOGGING_NAMESPACE)->info('requesting label from DHL');
        
        $jsonArray = ReturnOrderMapper::mapFromObjectToArray($returnOrder);
        logger(static::LOGGING_NAMESPACE)->debug("Input:\n" . print_r($jsonArray, true));
        $requestBody = json_encode($jsonArray);
        logger(static::LOGGING_NAMESPACE)->debug("Request body:\n{$requestBody}");
        
        $endpointUrl   = 'https://cig.dhl.de/services/production/rest/returns/';
        $cigUser       = 'gambio2_2';
        $cigPassword   = 'wkF0ib6JVJABwYZM730JX3CAw8DkA9';
        $isSandboxMode = $this->configurationService->get('mode') === 'sandbox';
        if ($isSandboxMode) {
            $endpointUrl   = 'https://cig.dhl.de/services/sandbox/rest/returns/';
            $cigUser       = $this->configurationService->get('cig/sandbox/user');
            $cigPassword   = $this->configurationService->get('cig/sandbox/password');
        }
        $username      = $this->configurationService->get('returns/user');
        $password      = $this->configurationService->get('returns/password');
        $userAuthToken = base64_encode("{$username}:{$password}");
        
        $returnOrderResponse = $this->client->post($endpointUrl,
                                                   [
                                                       'auth'    => [$cigUser, $cigPassword],
                                                       'headers' => [
                                                           'DPDHL-User-Authentication-Token' => $userAuthToken,
                                                           'Content-Type'                    => 'application/json',
                                                           'Accept'                          => 'application/json',
                                                       ],
                                                       'body'    => $requestBody
                                                   ]);
        
        $returnOrderConfirmationJson = $returnOrderResponse->getBody()->getContents();
        //logger(static::LOGGING_NAMESPACE)->debug("got response:\n" . $returnOrderConfirmationJson);
        
        $returnOrderConfirmationArray = \json_decode($returnOrderConfirmationJson, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $message = 'ERROR decoding JSON in response: ' . json_last_error();
            logger(static::LOGGING_NAMESPACE)->debug($message);
            throw new \RuntimeException($message);
        }
        
        if (isset($returnOrderConfirmationArray['shipmentNumber'], $returnOrderConfirmationArray['labelData'])) {
            logger(static::LOGGING_NAMESPACE)->debug('Label created, shipmentNumber '
                                                     . $returnOrderConfirmationArray['shipmentNumber']);
            $returnOrderConfirmation = new ReturnOrderConfirmation($returnOrderConfirmationArray['shipmentNumber']);
            $returnOrderConfirmation->setLabelData(base64_decode($returnOrderConfirmationArray['labelData']));
            //$returnOrderConfirmation->setQrLabelData();
            $returnOrderConfirmation->setRoutingCode($returnOrderConfirmationArray['routingCode'] ?? '');
            
            return $returnOrderConfirmation;
        }
        
        logger(static::LOGGING_NAMESPACE)->debug("unhandled response:\n" . $returnOrderConfirmationJson);
        
        throw new \RuntimeException('invalid response, shipmentNumber/labelData missing');
    }
}
