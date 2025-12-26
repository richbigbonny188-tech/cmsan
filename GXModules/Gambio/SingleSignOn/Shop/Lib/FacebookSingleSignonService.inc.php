<?php
/* --------------------------------------------------------------
   FacebookSingleSignonService.inc.php 2020-05-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class FacebookSingleSignonService extends AbstractSingleSignonService
{
    protected $loggerSubsystem = 'facebook';
    
    public function getAuthorizationLink()
    {
        $scope                               = 'public_profile email';
        $authorizationEndpoint               = 'https://www.facebook.com/v2.10/dialog/oauth';
        $state                               = sha1($this->generateRandomBytes(1024));
        $_SESSION['facebook_oauth_states']   = isset($_SESSION['facebook_oauth_states']) ? $_SESSION['facebook_oauth_states'] : [];
        $_SESSION['facebook_oauth_states'][] = $state;
        $_SESSION['facebook_oauth_states']   = array_slice($_SESSION['facebook_oauth_states'], -10);
        $loginUrl                            = $authorizationEndpoint . '?' . http_build_query([
                                                                                                   'app_id'        => $this->clientId,
                                                                                                   'response_type' => 'code',
                                                                                                   'scope'         => $scope,
                                                                                                   'state'         => $state,
                                                                                                   'redirect_uri'  => $this->redirectUri,
                                                                                               ]);
        
        return $loginUrl;
    }
    
    
    public function processAuthorizationCode($code)
    {
        $tokenEndpoint    = 'https://graph.facebook.com/v2.10/oauth/access_token';
        $tokenRequestData = [
            'code'          => $code,
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri'  => $this->redirectUri,
        ];
        $tokenUrl         = $tokenEndpoint . '?' . http_build_query($tokenRequestData);
        $tokenRequest     = MainFactory::create('RestRequest', 'GET', $tokenUrl);
        $tokenRestService = MainFactory::create('RestService');
        $tokenResponse    = $tokenRestService->performRequest($tokenRequest);
        $tokensData       = json_decode($tokenResponse->getResponseBody(), true);
        
        if (!empty($tokensData['error']) || empty($tokensData['access_token'])) {
            throw new Exception($tokensData['error']['message']);
        }
        
        $inspectTokenUrl      = 'https://graph.facebook.com/debug_token?' . http_build_query([
                                                                                                 'input_token'  => $tokensData['access_token'],
                                                                                                 'access_token' => $this->clientId
                                                                                                                   . '|'
                                                                                                                   . $this->clientSecret,
                                                                                             ]);
        $inspectTokenRequest  = MainFactory::create('RestRequest', 'GET', $inspectTokenUrl);
        $inspectTokenResponse = $tokenRestService->performRequest($inspectTokenRequest);
        $inspectTokenData     = json_decode($inspectTokenResponse->getResponseBody(), true);
        
        $profileUrl            = 'https://graph.facebook.com/v2.10/' . $inspectTokenData['data']['user_id'] . '?'
                                 . 'fields=id,birthday,email,first_name,gender,last_name,locale,verified';
        $profileRequestHeaders = [
            'Authorization: Bearer ' . $tokensData['access_token'],
        ];
        $profileRequest        = MainFactory::create('RestRequest', 'GET', $profileUrl, [], $profileRequestHeaders);
        $profileResponse       = $tokenRestService->performRequest($profileRequest);
        $profileData           = json_decode($profileResponse->getResponseBody(), true);
        
        $countryId = $countryId = $this->determineCountryByLocale($profileData['locale']);
        $password  = $this->makePassword();
        $ssoData   = [
            'iss'                 => 'facebook.com',
            'sub'                 => $profileData['id'],
            'customer_collection' => [
                'firstname'              => $profileData['first_name'],
                'lastname'               => $profileData['last_name'],
                'email_address'          => $profileData['email'],
                'email_address_confirm'  => $profileData['email'],
                'email_address_verified' => $profileData['verified'],
                'country'                => $countryId,
                'password'               => $password,
                'confirmation'           => $password,
            ],
            'identity'            => $profileData,
        ];
        
        return $ssoData;
    }
}
