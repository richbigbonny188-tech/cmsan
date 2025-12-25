<?php
/* --------------------------------------------------------------
   PaypalSingleSignonService.inc.php 2020-05-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class PaypalSingleSignonService extends AbstractSingleSignonService
{
    protected $mode = 'live';
    protected $loggerSubsystem = 'paypal';
    
    
    public function getAuthorizationLink()
    {
        $scopeElements = [
            'openid',
            'profile',
            // 'email',   // requires individual review by PayPal
            // 'address', // requires individual review by PayPal
            // 'phone',   // no longer supported
            // 'https://uri.paypal.com/services/paypalattributes',
        ];
        
        $nonce                             = sha1($this->generateRandomBytes(1024));
        $_SESSION['paypal_oauth_nonces']   = isset($_SESSION['paypal_oauth_nonces']) ? $_SESSION['paypal_oauth_nonces'] : [];
        $_SESSION['paypal_oauth_nonces'][] = $nonce;
        $_SESSION['paypal_oauth_nonces']   = array_slice($_SESSION['paypal_oauth_nonces'], -10);
        
        if ($this->mode === 'live') {
            $authorizationEndpoint = 'https://www.paypal.com/signin/authorize';
        } else {
            $authorizationEndpoint = 'https://www.sandbox.paypal.com/signin/authorize';
        }
        $authorizationLink = $authorizationEndpoint . '?' . http_build_query([
                                                                                 'client_id'     => $this->clientId,
                                                                                 'response_type' => 'code',
                                                                                 'scope'         => implode(' ',
                                                                                                            $scopeElements),
                                                                                 'redirect_uri'  => $this->redirectUri,
                                                                                 'nonce'         => $nonce,
                                                                             ]);
        $this->getLogger()->debug('Using authorization link {url}', ['url' => $authorizationLink]);
        
        return $authorizationLink;
    }
    
    
    public function processAuthorizationCode($code)
    {
        if ($this->mode === 'live') {
            $tokenEndpoint    = 'https://api.paypal.com/v1/oauth2/token';
            $identityEndpoint = 'https://api.paypal.com/v1/identity/openidconnect/userinfo?schema=openid';
        } else {
            $tokenEndpoint    = 'https://api.sandbox.paypal.com/v1/oauth2/token';
            $identityEndpoint = 'https://api.sandbox.paypal.com/v1/identity/openidconnect/userinfo?schema=openid';
        }
        
        $restService = MainFactory::create('RestService');
        
        $clientTokenRequestHeaders = [
            'Accept: application/json',
            'Accept-Language: en_US',
            'Content-Type: application/x-www-form-urlencoded',
        ];
        $clientTokenRequest        = MainFactory::create('RestRequest',
                                                         'POST',
                                                         $tokenEndpoint,
                                                         ['grant_type' => 'client_credentials'],
                                                         $clientTokenRequestHeaders);
        $clientTokenRequest->setUserpass($this->clientId . ':' . $this->clientSecret);
        $clientTokenResponse = $restService->performRequest($clientTokenRequest);
        $clientTokenData     = json_decode($clientTokenResponse->getResponseBody(), true);
        $clientAccessToken   = $clientTokenData['access_token'];
        
        $tokenRequestData  = http_build_query([
                                                  'code'         => $code,
                                                  'redirect_uri' => $this->redirectUri,
                                                  'grant_type'   => 'authorization_code',
                                              ]);
        $tokenRequest      = MainFactory::create('RestRequest',
                                                 'POST',
                                                 $tokenEndpoint,
                                                 $tokenRequestData,
                                                 ['Authorization: Bearer ' . $clientAccessToken]);
        $tokenResponse     = $restService->performRequest($tokenRequest);
        $tokensData        = json_decode($tokenResponse->getResponseBody(), true);
        $openidAccessToken = $tokensData['access_token'];
        
        $identityRequest  = MainFactory::create('RestRequest',
                                                'GET',
                                                $identityEndpoint,
                                                [],
                                                ['Authorization: Bearer ' . $openidAccessToken]);
        $identityResponse = $restService->performRequest($identityRequest);
        $idData           = json_decode($identityResponse->getResponseBody(), true);
        
        if (!array_key_exists('given_name', $idData)) {
            if (preg_match('/^([\w .-]+)\s([\w-]+)$/u', $idData['name'], $nameMatches) === 1) {
                $idData['given_name']  = $nameMatches[1];
                $idData['family_name'] = $nameMatches[2];
            } else {
                $idData['given_name']  = '';
                $idData['family_name'] = $idData['name'];
            }
        }
        
        $password = $this->makePassword();
        $ssoData  = [
            'iss'                 => 'paypal.com',
            'sub'                 => $idData['user_id'],
            'customer_collection' => [
                'firstname'              => $idData['given_name'],
                'lastname'               => $idData['family_name'],
                'email_address'          => $idData['email'],
                'email_address_confirm'  => $idData['email'],
                'email_address_verified' => $idData['verified_account'] === 'true',
                'country'                => $this->determineCountryByLocale($idData['language']),
                'street_address'         => $idData['address']['street_address'],
                'city'                   => $idData['address']['locality'],
                'postcode'               => $idData['address']['postal_code'],
                'telephone'              => $idData['phone_number'],
                'password'               => $password,
                'confirmation'           => $password,
            ],
            'identity'            => $idData,
        ];
        
        return $ssoData;
    }
    
    
    /**
     * Sets operation mode.
     *
     * Valid modes are 'sandbox' and 'live'. Default is 'live'.
     *
     * @param $mode
     *
     * @throws UnsupportedPaypalModeException
     */
    public function setMode($mode)
    {
        if (!in_array($mode, ['live', 'sandbox'], true)) {
            throw new UnsupportedPaypalModeException('mode unsupported: ' . $mode);
        }
        $this->mode = $mode;
    }
}
