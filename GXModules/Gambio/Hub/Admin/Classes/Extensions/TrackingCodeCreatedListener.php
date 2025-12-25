<?php
/* --------------------------------------------------------------
   TrackingCodeCreatedListener.inc.php 2023-06-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace GXModules\Gambio\Hub\Admin\Classes\Extensions;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\TrackingCode\Model\Events\TrackingCodeCreated;
use Gambio\Admin\Modules\TrackingCode\Model\TrackingCode;
use Gambio\Admin\Modules\TrackingCode\Services\TrackingCodeRepository;
use Gambio\Core\Configuration\Services\ConfigurationFinder;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;
use function Gambio\Core\Logging\logger;

class TrackingCodeCreatedListener
{
    /**
     * @var \Gambio\Admin\Modules\TrackingCode\Services\TrackingCodeRepository
     */
    private $trackingCodeRepository;
    
    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $connection;
    
    /**
     * @var \Gambio\Core\Configuration\Services\ConfigurationFinder
     */
    private $configurationFinder;
    
    
    public function __construct(
        TrackingCodeRepository $trackingCodeRepository,
        Connection             $connection,
        ConfigurationFinder    $configurationFinder
    ) {
        $this->trackingCodeRepository = $trackingCodeRepository;
        $this->connection             = $connection;
        $this->configurationFinder    = $configurationFinder;
    }
    
    
    private function isPayPal2HubOrder(int $orderId): bool
    {
        $qb       = $this->connection->createQueryBuilder();
        $orderRow = $qb->select('payment_class', 'gambio_hub_module')
            ->from('orders')
            ->where("orders_id = {$orderId}")
            ->executeQuery()
            ->fetchAssociative();
        if (empty($orderRow)) {
            return false;
        }
        
        return $orderRow['payment_class'] === 'gambio_hub' && $orderRow['gambio_hub_module'] === 'PayPal2Hub';
    }
    
    
    private function sendToHub(TrackingCode $trackingCode): void
    {
        $gambioHubUrl = $this->configurationFinder->get('configuration/MODULE_PAYMENT_GAMBIO_HUB_URL');
        $clientKey    = $this->configurationFinder->get('gm_configuration/GAMBIO_HUB_CLIENT_KEY');
        $timeout      = $this->configurationFinder->get('gm_configuration/GAMBIO_HUB_CURL_TIMEOUT');
        
        $url      = "{$gambioHubUrl}/payment_modules/PayPal2Hub/callback";
        $getData  = ['source' => 'tracking_code_created'];
        $url      .= '?' . http_build_query($getData, '', '&', PHP_QUERY_RFC3986);
        $client   = new Client(['timeout' => (float)$timeout,]);
        $postData = [
            'client_key'    => $clientKey,
            'order_id'      => $trackingCode->orderId(),
            'tracking_code' => $trackingCode->code(),
            'carrier'       => $trackingCode->parcelServiceName(),
        ];
        
        try {
            $response = $client->post($url, [
                'headers'     => [
                    'Accept' => 'application/json',
                ],
                'form_params' => $postData,
            ]);
        } catch (ServerException $e) {
            logger('hub')->warning("Could not send tracking_code_created to PayPal2Hub, http 500:\n{$e->getMessage()}");
            
            return;
        }
        
        if ($response->getStatusCode() !== 200) {
            logger('hub')->warning("Could not send tracking_code_created to PayPal2Hub, http {$response->getStatusCode()}:\n{$response->getBody()->getContents()}");
        } else {
            logger('hub')->debug("Sent tracking_code_created, response ({$response->getStatusCode()}):\n{$response->getBody()->getContents()}");
        }
    }
    
    
    public function __invoke(TrackingCodeCreated $codeCreated)
    {
        $trackingCode = $this->trackingCodeRepository->getTrackingCodeById($codeCreated->trackingCodeId());
        if (!$trackingCode->isReturnDelivery() && $this->isPayPal2HubOrder($trackingCode->orderId())) {
            logger('hub')->debug("Tracking number {$trackingCode->code()} for carrier {$trackingCode->parcelServiceName()} created for PayPal2Hub order {$trackingCode->orderId()}");
            $this->sendToHub($trackingCode);
        }
    }
}
