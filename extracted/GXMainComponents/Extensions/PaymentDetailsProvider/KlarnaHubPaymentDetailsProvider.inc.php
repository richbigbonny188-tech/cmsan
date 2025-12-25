<?php
/* --------------------------------------------------------------
   KlarnaHubPaymentDetailsProvider.inc.php 2018-06-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use \HubPublic\Http\CurlRequest;

abstract class KlarnaHubPaymentDetailsProvider implements PaymentDetailsProvider
{
    protected $moduleCode = 'KlarnaHub';
    
    
    public function getDetails(IdType $orderId)
    {
        $query = [
            'client_key' => gm_get_conf('GAMBIO_HUB_CLIENT_KEY'),
            'devmode'    => file_exists(DIR_FS_CATALOG . '/.dev-environment') ? 'true' : 'false',
            'order_id'   => $orderId->asInt(),
        ];
        /** @var HubSettings $hubSettings */
        $hubSettings = MainFactory::create('HubSettings', gm_get_conf('GAMBIO_HUB_CURL_TIMEOUT'));
        
        /** @var \HubCallbackApiClient $hubCallbackApiClient */
        $hubCallbackApiClient = MainFactory::create('HubCallbackApiClient',
                                                    MODULE_PAYMENT_GAMBIO_HUB_URL,
                                                    new CurlRequest(),
                                                    LogControl::get_instance(),
                                                    $hubSettings);
        try {
            /** @var \HttpResponse $response */
            $response = $hubCallbackApiClient->execute($this->moduleCode,
                                                       true,
                                                       ['source' => 'payment_details'],
                                                       $query);
            
            if ($response->getStatusCode() !== 200) {
                throw new RuntimeException('Error retrieving data from hub');
            }
            
            $responseBody = json_decode($response->getBody(), true);
        } catch (Exception $exception) {
            // Suppress the settlements callback error as older Hub releases won't support it anyway.
            $responseBody = [
                'error' => 'data unavailable: ' . $exception->getMessage(),
            ];
        }
        
        $details = [
            'hubdetails' => $responseBody,
        ];
        
        return $details;
    }
}

