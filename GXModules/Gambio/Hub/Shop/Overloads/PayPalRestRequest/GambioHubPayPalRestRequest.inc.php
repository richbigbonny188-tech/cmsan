<?php
/* --------------------------------------------------------------
   GambioHubPayPalRestRequest.inc.php 2021-04-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use \HubPublic\Http\CurlRequest;

class GambioHubPayPalRestRequest extends GambioHubPayPalRestRequest_parent
{
    const MINIMUM_ACCESSTOKEN_VALIDITY = 600;


    public function __construct($method, $url, $data = null, $mode = 'ecm')
    {
        if ($mode === 'inst_hub' || $mode === 'hub') {
            $this->configStorage = MainFactory::create_object('PayPalConfigurationStorage');
            $hubConfiguration    = MainFactory::create('HubPayPalConfiguration');
            $endpointMode        = $hubConfiguration->getEnv() === 'sandbox' ? 'sandbox' : 'live';
            $bn_id               = self::BN_ID_INST;
            $headers             = [
                'Authorization: Bearer ' . $this->getAccessTokenViaGambioHub(),
                'Accept: application/json',
                'Accept-Language: en_US',
                'Content-Type: application/json',
                'PayPal-Partner-Attribution-Id: ' . $bn_id,
                'Expect:',
            ];

            $this->setMethod($method);
            if (strpos($url, 'https://') !== 0) {
                $url = $this->configStorage->get('service_base_url/' . $endpointMode) . $url;
            }
            $this->setURL($url);
            $this->setData($data);
            $this->setHeaders($headers);
        } else {
            parent::__construct($method, $url, $data, $mode);
        }
    }


    public function getAccessTokenViaGambioHub()
    {
        $accessToken = null;
    
        // Try the cached value from within the gm_configuration table.
        $cachedValue = gm_get_conf('GAMBIO_HUB_PAYPAL_HUB_ACCESS_TOKEN');
    
        if (!empty($cachedValue)) {
            $accessToken = json_decode($cachedValue, true);
            $expiresAt   = (int)$accessToken['createdAt'] + (int)$accessToken['expiresIn'];
            if (($expiresAt + self::MINIMUM_ACCESSTOKEN_VALIDITY) < time()) {
                $accessToken = null;
            }
        }

        if ($accessToken === null) {
            $accessTokenData = $this->fetchAccessToken();
            if (isset($accessTokenData['accessToken'])) {
                $accessToken = $accessTokenData['accessToken'];
                $_SESSION['PayPalInstalmentsHub_IUP_AccessToken'] = $accessToken;
                gm_set_conf('GAMBIO_HUB_PAYPAL_HUB_ACCESS_TOKEN', json_encode($accessToken));
            } else {
                throw new \RuntimeException('Could not retrieve access token');
            }
        }
        return $accessToken['accessToken'];
    }


    protected function fetchAccessToken()
    {
        $query = [
            'client_key' => gm_get_conf('GAMBIO_HUB_CLIENT_KEY'),
            'action'     => 'getAccessToken',
        ];
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

        try {
            /** @var \HttpResponse $response */
            $response = $hubCallbackApiClient->execute(
                'PayPal2Hub',
                true,
                ['source' => 'get_accesstoken'],
                $query
            );

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

        return $responseBody;
    }

}
