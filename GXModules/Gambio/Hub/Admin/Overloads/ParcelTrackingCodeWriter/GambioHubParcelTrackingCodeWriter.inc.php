<?php
/* --------------------------------------------------------------
   GambioHubParcelTrackingCodeWriter.inc.php 2023-05-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

use GuzzleHttp\Client;
use function Gambio\Core\Logging\logger;

class GambioHubParcelTrackingCodeWriter extends GambioHubParcelTrackingCodeWriter_parent
{
    public function insertTrackingCode(
        $p_orderId,
        $p_trackingCode,
        $p_parcelServiceId,
        ParcelServiceReader $parcelServiceReadService
    ) {
        $rc = parent::insertTrackingCode($p_orderId, $p_trackingCode, $p_parcelServiceId, $parcelServiceReadService);
        
        if (strtolower((string)@constant('MODULE_PAYMENT_GAMBIO_HUB_STATUS')) === 'true'
            && $this->isPayPal2HubOrder((int)$p_orderId)) {
            logger('hub')->debug("Tracking code {$p_trackingCode} for order {$p_orderId} created via legacy ParcelTrackingCodeWriter, sending to Hub");
            /** @var ParcelService $parcelService */
            $parcelService = $parcelServiceReadService->getParcelServiceById($p_parcelServiceId);
            $this->sendTrackingCodeToHub((int)$p_orderId, $parcelService->getName(), (string)$p_trackingCode);
        }
        
        return $rc;
    }
    
    
    private function isPayPal2HubOrder(int $orderId): bool
    {
        /** @var OrderReadServiceInterface $orderReadService */
        $orderReadService = StaticGXCoreLoader::getService('OrderRead');
        /** @var OrderInterface $order */
        $order = $orderReadService->getOrderById(new IdType(($orderId)));
        
        return $order->getPaymentType()->getPaymentClass() === 'gambio_hub'
               && $order->getPaymentType()->getModule() === 'PayPal2Hub';
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
