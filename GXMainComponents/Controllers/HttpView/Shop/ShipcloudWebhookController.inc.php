<?php
/* --------------------------------------------------------------
   ShipcloudWebhookController.inc.php 2020-07-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class ShipcloudWebhookController extends HttpViewController
{
    /**
     * @var \LanguageTextManager
     */
    protected $text;
    
    /**
     * @var \ShipcloudLogger
     */
    protected $logger;
    
    protected static $knownTypes = [
        'shipment.tracking.label_created',
        'shipment.tracking.picked_up',
        'shipment.tracking.transit',
        'shipment.tracking.out_for_delivery',
        'shipment.tracking.delivered',
        'shipment.tracking.awaits_pickup_by_receiver',
        'shipment.tracking.canceled',
        'shipment.tracking.delayed',
        'shipment.tracking.exception',
        'shipment.tracking.not_delivered',
        'shipment.tracking.destroyed',
        'shipment.tracking.notification',
        'shipment.tracking.unknown',
    ];
    
    
    protected function init()
    {
        $this->text   = MainFactory::create('LanguageTextManager', 'shipcloud', $_SESSION['language_id']);
        $this->logger = MainFactory::create('ShipcloudLogger');
    }
    
    
    public function actionDefault()
    {
        $responseData = [
            'result' => 'OK',
        ];

        try {
            $rawInput = file_get_contents('php://input');
            $this->logger->debug_notice(sprintf("WEBHOOK called from %s, body:\n%s",
                                                $_SERVER['REMOTE_ADDR'],
                                                $rawInput));
            $webhookData = json_decode($rawInput, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new ShipcloudWebhookException('invalid input');
            }
            
            if(!isset($webhookData['type']) || !in_array($webhookData['type'], static::$knownTypes, true)) {
                throw new ShipcloudWebhookException('unhandled message type');
            }
            
            if (!isset($webhookData['data']['object_type']) || $webhookData['data']['object_type'] !== 'shipment') {
                throw new ShipcloudWebhookException('unsupported object_type');
            }
            
            if (empty($webhookData['data']['url'])) {
                throw new ShipcloudWebhookException('no shipment URL');
            }

            try {
                $shipmentsRequest = MainFactory::create('ShipcloudRestRequest', 'GET', $webhookData['data']['url']);
                $restService      = MainFactory::create('ShipcloudRestService');
                $result           = $restService->performRequest($shipmentsRequest);
            } catch (RestException $e) {
                throw new ShipcloudWebhookException($e->getMessage());
            }
            
            $shipmentData = json_decode($result->getResponseBody(), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new ShipcloudWebhookException('could not retrieve shipment data');
            }
            
            if ($result->getResponseCode() !== 200) {
                throw new ShipcloudWebhookException('Error retrieving shipment data - ' . $result->getResponseCode());
            }
            
            if (empty($shipmentData['reference_number'])) {
                throw new ShipcloudWebhookException('Shipment referenced in webhook notification does not have a reference_number');
            }
            $orderId = new IdType((int)$shipmentData['reference_number']);
            
            $this->logger->notice(sprintf('WEBHOOK %s/%s for order %s',
                                          $webhookData['id'],
                                          $webhookData['type'],
                                          (string)$orderId->asInt()));
            
            $this->processWebhookEvent($orderId, new StringType($webhookData['type']), $shipmentData);
            
        } catch (ShipcloudWebhookException $e) {
            $this->logger->notice('ERROR handling webhook event: ' . $e->getMessage());
            $responseData = [
                'result' => 'OK/NOTICE',
                'messages' => [
                    $e->getMessage(),
                ]
            ];
        } catch (UnexpectedValueException $e) {
            $this->logger->notice('ERROR handling webhook event: ' . $e->getMessage());
            $responseData = [
                'result' => 'OK/NOTICE',
                'messages' => [
                    $e->getMessage(),
                ]
            ];
        }
        
        $response = MainFactory::create('JsonHttpControllerResponse',
                                        $responseData,
                                        ['Content-Type: application/json']);
        
        return $response;
    }
    
    
    protected function processWebhookEvent(IdType $orderId, StringType $eventType, array $shipmentData)
    {
        /** @var \OrderWriteService $orderWrite */
        $orderWrite = StaticGXCoreLoader::getService('OrderWrite');
        /** @var \OrderReadService $orderRead */
        $orderRead        = StaticGXCoreLoader::getService('OrderRead');
        $oldOrderStatusId = new IdType($orderRead->getOrderById($orderId)->getStatusId());
        
        if (in_array($eventType->asString(), static::$knownTypes, true)) {
            /** @var \ShipcloudConfigurationStorage $configuration */
            $configuration               = MainFactory::create('ShipcloudConfigurationStorage');
            $orderStatusConfigurationKey = str_replace('.', '_', $eventType->asString());
            $orderStatusConfigurationKey = str_replace('shipment_', '', $orderStatusConfigurationKey);
            $orderStatusId               = (int)$configuration->get('webhook/order_status_'
                                                                    . $orderStatusConfigurationKey);
            $historyComment              = $this->text->get_text('webhook_event_' . str_replace('.',
                                                                                                '_',
                                                                                                $eventType->asString()));
            if (!empty($shipmentData['carrier_tracking_no'])) {
                $historyComment .= "\n" . $this->text->get_text('tracking_no') . ': '
                                   . $shipmentData['carrier_tracking_no'];
            }
            if (!empty($shipmentData['tracking_url'])) {
                $historyComment .= "\n" . $this->text->get_text('tracking_link') . ': '
                                   . $shipmentData['tracking_url'];
            }
        } else {
            $orderStatusId  = -1; // do not change
            $historyComment = $this->text->get_text('webhook_event_eventtype_unsupported') . ': '
                              . $eventType->asString();
        }
        if ($orderStatusId >= 0) {
            $newOrderStatusId = new IdType($orderStatusId);
        } else {
            $newOrderStatusId = $oldOrderStatusId;
        }
        $orderWrite->updateOrderStatus($orderId,
                                       $newOrderStatusId,
                                       new StringType($historyComment),
                                       new BoolType(false));
    }
}

class ShipcloudWebhookException extends RuntimeException
{
}
