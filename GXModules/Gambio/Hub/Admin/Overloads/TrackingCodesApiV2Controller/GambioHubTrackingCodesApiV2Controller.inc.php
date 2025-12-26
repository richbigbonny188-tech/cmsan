<?php
/* --------------------------------------------------------------
   GambioHubTrackingCodesApiV2Controller.inc.php 2023-05-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;
use function Gambio\Core\Logging\logger;

class GambioHubTrackingCodesApiV2Controller extends GambioHubTrackingCodesApiV2Controller_parent
{
    protected $isPost = false;
    
    
    public function post()
    {
        $this->isPost = true;
        parent::post();
    }
    
    
    protected function _writeResponse(array $response, $p_statusCode = 200)
    {
        parent::_writeResponse($response, $p_statusCode);
        if ($this->isPost && strtolower((string)@constant('MODULE_PAYMENT_GAMBIO_HUB_STATUS')) === 'true') {
            $orderId = (int)($response['orderId'] ?? 0);
            if ($orderId > 0 && $this->isPayPal2HubOrder($orderId)) {
                $trackingCode = $response['trackingCode'] ?? '';
                $carrier      = $response['parcelServiceName'] ?? '';
                logger('hub')->info("Sending tracking code {$trackingCode} ({$carrier}) generated via REST API v2 for order {$orderId} to Hub");
                $this->sendTrackingCodeToHub($orderId, $carrier, $trackingCode);
            }
        }
    }
    
    
    private function isPayPal2HubOrder(int $orderId): bool
    {
        try {
            /** @var OrderReadServiceInterface $orderReadService */
            $orderReadService = StaticGXCoreLoader::getService('OrderRead');
            /** @var OrderInterface $order */
            $order = $orderReadService->getOrderById(new IdType(($orderId)));
    
            return $order->getPaymentType()->getPaymentClass() === 'gambio_hub'
                   && $order->getPaymentType()->getModule() === 'PayPal2Hub';
        } catch (UnexpectedValueException $e) {
            return false;
        }
    }
    
    
    private function sendTrackingCodeToHub(int $orderId, string $carrier, string $trackingCode): void
    {
        $gambioHubUrl = MODULE_PAYMENT_GAMBIO_HUB_URL;
        $clientKey    = gm_get_conf('GAMBIO_HUB_CLIENT_KEY');
        $timeout      = gm_get_conf('GAMBIO_HUB_CURL_TIMEOUT');
        
        $url      = "{$gambioHubUrl}/payment_modules/PayPal2Hub/callback";
        $getData  = ['source' => 'tracking_code_created'];
        $url      .= '?' . http_build_query($getData, '', '&', PHP_QUERY_RFC3986);
        $client   = new Client(['timeout' => (float)$timeout,]);
        $postData = [
            'client_key'    => $clientKey,
            'order_id'      => $orderId,
            'tracking_code' => $trackingCode,
            'carrier'       => $carrier,
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
}
