<?php
/* --------------------------------------------------------------
   CashFlowTechHubClient.inc.php 2019-07-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use \HubPublic\Http\CurlRequest;

class CashFlowTechHubClient
{
    /**
     * Retrieves mandator status from Hub.
     *
     * @return array with keys 'hasMandatorId' (bool) and 'mandatorStatus' (string)
     */
    public function getMandatorStatusFromHub()
    {
        /** @var HubSettings $hubSettings */
        $hubSettings = MainFactory::create('HubSettings', gm_get_conf('GAMBIO_HUB_CURL_TIMEOUT'));
        /** @var \HubCallbackApiClient $hubCallbackApiClient */
        $hubCallbackApiClient = MainFactory::create(
            'HubCallbackApiClient',
            MODULE_PAYMENT_GAMBIO_HUB_URL,
            new CurlRequest(),
            LogControl::get_instance(),
            $hubSettings
        );
        
        /** @var \HttpResponse $response */
        $response = $hubCallbackApiClient->execute(
            'CashFlowTechHub',
            false,
            [
                'action' => 'CheckMandatorStatus',
                'clientkey' => gm_get_conf('GAMBIO_HUB_CLIENT_KEY')
            ]
        );
        if($response->getStatusCode() !== 200)
        {
            throw new RuntimeException('Error getting mandator status from Hub');
        }
        
        $responseBody = json_decode($response->getBody(), true);
        return $responseBody;
    }
    
    
    /**
     * Returns an array with collections data for a given orders_id
     *
     * @param $ordersId
     *
     * @return array
     */
    public function getCollectionStatus($ordersId)
    {
        /** @var HubSettings $hubSettings */
        $hubSettings = MainFactory::create('HubSettings', gm_get_conf('GAMBIO_HUB_CURL_TIMEOUT'));
        /** @var \HubCallbackApiClient $hubCallbackApiClient */
        $hubCallbackApiClient = MainFactory::create(
            'HubCallbackApiClient',
            MODULE_PAYMENT_GAMBIO_HUB_URL,
            new CurlRequest(),
            LogControl::get_instance(),
            $hubSettings
        );
    
        /** @var \HttpResponse $response */
        $response = $hubCallbackApiClient->execute(
            'CashFlowTechHub',
            false,
            [
                'action' => 'getCollectionStatus',
                'clientkey' => gm_get_conf('GAMBIO_HUB_CLIENT_KEY'),
                'orders_id' => (int)$ordersId,
            ]
        );
        if($response->getStatusCode() !== 200)
        {
            throw new RuntimeException('Error getting collection status from Hub');
        }
    
        $responseBody = json_decode($response->getBody(), true);
        return $responseBody;
    }
    
    
    public function createNewCollection($orderId, $collectionData, $invoiceData, $invoices)
    {
        /** @var HubSettings $hubSettings */
        $hubSettings = MainFactory::create('HubSettings', gm_get_conf('GAMBIO_HUB_CURL_TIMEOUT'));
        /** @var \HubCallbackApiClient $hubCallbackApiClient */
        $hubCallbackApiClient = MainFactory::create(
            'HubCallbackApiClient',
            MODULE_PAYMENT_GAMBIO_HUB_URL,
            new CurlRequest(),
            LogControl::get_instance(),
            $hubSettings
        );
    
        /** @var \HttpResponse $response */
        $response = $hubCallbackApiClient->execute(
            'CashFlowTechHub',
            true,
            [
                'action'    => 'createNewCollection',
                'clientkey' => gm_get_conf('GAMBIO_HUB_CLIENT_KEY'),
            ],
            [
                'orderId'        => $orderId,
                'new-collection' => json_encode($collectionData),
                'invoiceData'    => $invoiceData,
                'invoices'       => json_encode($invoices),
            ]
        );
        if($response->getStatusCode() !== 200)
        {
            throw new RuntimeException('Error creating new collection');
        }
    
        $responseBody = json_decode($response->getBody(), true);
        return $responseBody;
    }

    
    public function createNewPayment($collectionId, $amount, $type)
    {
        /** @var HubSettings $hubSettings */
        $hubSettings = MainFactory::create('HubSettings', gm_get_conf('GAMBIO_HUB_CURL_TIMEOUT'));
        /** @var \HubCallbackApiClient $hubCallbackApiClient */
        $hubCallbackApiClient = MainFactory::create(
            'HubCallbackApiClient',
            MODULE_PAYMENT_GAMBIO_HUB_URL,
            new CurlRequest(),
            LogControl::get_instance(),
            $hubSettings
        );
    
        /** @var \HttpResponse $response */
        $response = $hubCallbackApiClient->execute(
            'CashFlowTechHub',
            true,
            [
                'action'    => 'createNewPayment',
                'clientkey' => gm_get_conf('GAMBIO_HUB_CLIENT_KEY'),
            ],
            [
                'collection_id'      => $collectionId,
                'new_payment_amount' => $amount,
                'new_payment_type'   => $type,
            ]
        );
        if($response->getStatusCode() !== 200)
        {
            throw new RuntimeException('Error creating new payment');
        }
    
        $responseBody = json_decode($response->getBody(), true);
        return $responseBody;
    }
    
    
    public function reversePayment($collectionId, $fokoId, $amount)
    {
        /** @var HubSettings $hubSettings */
        $hubSettings = MainFactory::create('HubSettings', gm_get_conf('GAMBIO_HUB_CURL_TIMEOUT'));
        /** @var \HubCallbackApiClient $hubCallbackApiClient */
        $hubCallbackApiClient = MainFactory::create(
            'HubCallbackApiClient',
            MODULE_PAYMENT_GAMBIO_HUB_URL,
            new CurlRequest(),
            LogControl::get_instance(),
            $hubSettings
        );
    
        /** @var \HttpResponse $response */
        $response = $hubCallbackApiClient->execute(
            'CashFlowTechHub',
            true,
            [
                'action'    => 'reversePayment',
                'clientkey' => gm_get_conf('GAMBIO_HUB_CLIENT_KEY'),
            ],
            [
                'collection_id'          => $collectionId,
                'foko_id'                => $fokoId,
                'reverse_payment_amount' => $amount,
            ]
        );
        if($response->getStatusCode() !== 200)
        {
            throw new RuntimeException('Error reversing payment');
        }
    
        $responseBody = json_decode($response->getBody(), true);
        return $responseBody;
    }
}
